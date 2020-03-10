<?php

namespace Daniesy\Rodels\Api;

use Daniesy\Rodels\Api\Auth\Authenticator;
use Daniesy\Rodels\Api\Components\Endpoint;
use Daniesy\Rodels\Api\Exceptions\InvalidEndpointException;
use Daniesy\Rodels\Api\Http\Curl;
use Daniesy\Rodels\Api\Http\HttpClient;
use Daniesy\Rodels\Api\Transport\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Remote
{
    /**
     * Namespace for the endpoints
     *
     * @var string
     */
    protected $endpointNamespace = "App\Endpoints";

    /**
     * A reference to the request class which travels
     * through the application
     *
     * @var Transport\Request
     */
    public $request;

    /**
     * A array containing the cached endpoints
     *
     * @var Collection
     */
    private $cachedEndpoints;
    /**
     * @var Authenticator
     */
    private $authenticator;

    /**
     * Client constructor.
     * @param Authenticator|null $authenticator
     * @throws \Exception
     */
    public function __construct(Authenticator $authenticator = null)
    {
        $this->cachedEndpoints = collect([]);
        $httpClient = $this->getHttpClient();
        $this->authenticator = $authenticator;
        $this->request = new Request(config('rodels.host'), $httpClient);
    }

    /**
     * @return HttpClient
     * @throws \Exception
     */
    private function getHttpClient(): HttpClient
    {
        switch (strtolower(config('rodels.client'))) {
            case 'guzzle':
                throw new \Exception('Guzzle not implemented.');
            case 'curl':
            default:
                return new Curl;
        }
    }

    /**
     * Get a requested API endpoint
     *
     * @param string $endpoint
     * @return Endpoint
     * @throws InvalidEndpointException
     */
    public function getEndpoint(string $endpoint)
    {
        $endpoint = Str::studly($endpoint);
        $class = sprintf("\\%s\\%s", $this->endpointNamespace, $endpoint);

        if (!class_exists($class)) {
            throw new InvalidEndpointException;
        }

        $endpointInstance = $this->cachedEndpoints->get($endpoint, null);

        if (!$endpointInstance) {
            $endpointInstance = new $class($this->request, $this->authenticator);
            $this->cachedEndpoints->put($endpoint, $endpointInstance);
        }

        return $endpointInstance;
    }

    /**
     * Get a requested API endpoint
     *
     * @param string $endpoint
     * @return mixed
     * @throws InvalidEndpointException
     */
    public function __get(string $endpoint)
    {
        return $this->getEndpoint($endpoint);
    }

    /**
     * @param string $name
     * @param $args
     * @return Endpoint|mixed
     * @throws InvalidEndpointException
     */
    public function __call(string $name, $args)
    {
        if (method_exists($this, $name)) {
            return call_user_func_array(array($this, $name), $args);
        }
        return $this->getEndpoint($name);
    }

}
