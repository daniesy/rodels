<?php


namespace Daniesy\Rodels\Api\Components;


use Daniesy\Rodels\Api\Transport\Response;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Rodel implements \JsonSerializable
{
    /**
     * The name associated with the model.
     *
     * @var string
     */
    protected $name;

    /**
     * Indicates if the model should have timestamps
     *
     * @var bool
     */
    protected $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The model's attributes
     *
     * @var Collection
     */
    protected $attributes;

    /**
     * Create a new model instance
     *
     * @param null $attributes
     */
    public function __construct($attributes = null)
    {
        $this->attributes = collect([]);
        if ($attributes instanceof Response) {
            $this->fill($attributes->data);
        } else if (is_array($attributes)) {
            $this->fill($attributes);
        } else if ($attributes instanceof Collection) {
            $this->attributes = $attributes;
        }
    }

    /**
     * Fill the attributes
     *
     * @param array $attributes
     */
    private function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param $value
     *
     * @return Rodel
     */
    public function setAttribute(string $key, $value): self
    {
        if ($this->hasSetMutator($key)) {
            $method = $this->generateMutatorMethod($key, 'set');
            return $this->{$method}($value);
        }

        // If an attribute is listed as a "date", we'll convert it from a DateTime
        // instance into a form proper for storage on the database tables using
        // the connection grammar's date format. We will auto set the values.
        if ($value && in_array($key, $this->getDates())) {
            $value = $this->asDateTime($value);
        }

        $this->attributes->put($key, $value);

        return $this;
    }

    /**
     * @param $value
     * @return Carbon
     */
    private function asDateTime($value):Carbon
    {
        // If the value is already a Carbon instance, we will just skip the rest of
        // these checks since they will be a waste of time, and hinder performance
        // when checking the field. We will just return the Carbon right away.
        if ($value instanceof Carbon) {
            //
        }

        // If this value is an integer, we will assume it is a UNIX timestamp's value
        // and format a Carbon object from this timestamp. This allows flexibility
        // when defining your date fields as they might be UNIX timestamps here.
        elseif (is_numeric($value)) {
            $date = new Carbon();
            return $date->setTimestamp($value);
        }

        // If the value is in simply year, month, day format, we will instantiate the
        // Carbon instances from that format. Again, this provides for simple date
        // fields on the database, while still supporting Carbonized conversion.
        elseif (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value)) {
            return Carbon::createFromFormat('Y-m-d', $value);
        }

        // If the value is in simply hour, minute, second format, we will instantiate the
        // Carbon instances from that format.
        elseif (preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $value)) {
            return Carbon::createFromFormat('H:i:s', $value);
        }

        // If the value is in zulu format, we will instantiate the
        // Carbon instances from that format.
        elseif (preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})Z$/', $value)) {
            return Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $value);
        }

        return new Carbon($value->format('Y-m-d H:i:s.u'), $value->getTimeZone());
    }

    /**
     * Get the value of an attribute
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute(string $key)
    {
        $value = $this->attributes->get($key);

        // Check the presence of a mutator for the get operations.
        if ($this->hasGetMutator($key)) {
            $method = $this->generateMutatorMethod($key, 'get');
            return $this->{$method}($value);
        }

        return $value;
    }

    /**
     * Generates a mutator method name for a given attribute.
     *
     * @param string $key
     * @param string $type
     * @return string
     */
    private function generateMutatorMethod(string $key, string $type = 'set'): string
    {
        return sprintf("%s%sAttribute", $type, Str::studly($key));
    }

    /**
     * Check if a set mutator exists for a given attribute key.
     *
     * @param string $key
     * @return bool
     */
    private function hasSetMutator(string $key): bool
    {
        return method_exists($this, $this->generateMutatorMethod($key, 'set'));
    }

    /**
     * Check if a get mutator exists for a given attribute key.
     *
     * @param string $key
     * @return bool
     */
    private function hasGetMutator(string $key): bool
    {
        return method_exists($this, $this->generateMutatorMethod($key, 'get'));
    }

    /**
     * Get the attributes that should be converted to date objects.
     *
     * @return array
     */
    private function getDates(): array
    {
        return $this->timestamps
            ? array_merge(['created_at', 'updated_at'], $this->dates)
            : $this->dates;
    }

    /**
     * Convert the instance to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->attributes->map(function($attribute, string $key) {
            if ($attribute instanceof Carbon) {
                return $attribute->format('Y-m-d\TH:i:s\Z');
            }
            return $attribute;
        })->toArray();
    }


    /**
     * Convert the instance to json
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), true);
    }

    /**
     * Convert the instance into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Get the model's attribute
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Set the model's attribute
     *
     * @param  string $key
     * @param  mixed  $value
     */
    public function __set(string $key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Check if the model's attribute is set
     *
     * @param $key
     *
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return $this->attributes->has($key);
    }

    /**
     * Convert the model to its string representation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

}
