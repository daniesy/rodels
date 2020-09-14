<?php


namespace Daniesy\Rodels\Api\Components;


use Daniesy\Rodels\Api\Exceptions\InvalidModelException;
use Daniesy\Rodels\Api\Transport\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class RodelCollection implements \JsonSerializable, \ArrayAccess, \IteratorAggregate
{
    /**
     * @var Collection
     */
    private $items;

    /**
     * Stores the meta object
     *
     * @var array
     */
    public $meta = [];
    /**
     * Response instance
     *
     * @var Response
     */
    private $response;

    /**
     * CollectionModel constructor.
     * @param array|Collection|Response $items
     * @param string $model
     * @throws InvalidModelException
     * @throws \Exception
     */
    public function __construct($items, string $model)
    {
        if (!class_exists($model)) {
            throw new InvalidModelException($model);
        }
        // Get items and response instance;
        if ($items instanceof Response) {
            $this->response = $items;
            $this->meta = $items->meta ?: [];
            $this->items = collect($items->data);
        } else if (is_array($items)) {
            $this->response = null;
            $this->items = collect($items);
        } else if ($items instanceof Collection) {
            $this->response = null;
            $this->items = $items;
        } else {
            throw new \Exception("{$items} needs to be an instance of Response, Collection or array");
        }

        $this->items = $this->mapItemsToModels($this->items, $model);
    }

    private function mapItemsToModels(Collection $items, string $model)
    {
        return $items->map(function ($item) use($model) {
            return new $model($item);
        });
    }

    /**
     * Get an item from meta data using "dot" notation
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getMeta($key, $default = null)
    {
        return array_get($this->meta, $key, $default);
    }

    /**
     * Set an item in meta data using "dot" notation
     *
     * @param $key
     * @param $value
     * @return array
     */
    private function setMeta($key, $value)
    {
        return array_set($this->meta, $key, $value);
    }

    /**
     * Get the total number of items
     *
     * @return int
     */
    public function totalItems(): int
    {
        $total = $this->getMeta("pagination.total", null);

        if (is_null($total)) {
            $this->setMeta("pagination.total", $total = $this->items->count());
        }

        return intval($total);
    }

    /**
     * Get the amount of items per page
     *
     * @return int
     */
    public function itemsPerPage(): int
    {
        return intval($this->getMeta("pagination.per_page", -1));
    }

    /**
     * Get the current pagination page
     *
     * @return int
     */
    public function currentPage(): int
    {
        return intval($this->getMeta('pagination.current_page', -1));
    }

    /**
     * Get the total pagination pages
     *
     * @return int
     */
    public function totalPages(): int
    {
        return intval($this->getMeta('pagination.total_pages', -1));
    }

    /**
     * Return a Laravel pagination instance.
     *
     * @return LengthAwarePaginator
     */
    public function paginate(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $this->items,
            $this->totalItems(),
            $this->itemsPerPage(),
            $this->currentPage(), [
                'path' => LengthAwarePaginator::resolveCurrentPath()
            ]
        );
    }

    /**
     * Determine if there are more items in the data source
     *
     * @return bool
     */
    public function hasMorePages(): bool
    {
        return $this->currentPage() < $this->totalPages();
    }


    /**
     * Get all items from the collection
     *
     * @return Collection
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Return a meta attribute
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return array_get($this->meta, $key, null);
    }


    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array
    {
        $meta = $this->meta;
        $data = $this->items->map(function(Rodel $item) {
            return $item->toArray();
        });

        return compact('meta', 'data');
    }

    /**
     * Convert to json
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), true);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the collection to its string representation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->items->getIterator();
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return $this->items->offsetExists($key);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->items->offsetGet($key);
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        return $this->items->offsetSet($key, $value);
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        return $this->items->offsetUnset($key);
    }

}
