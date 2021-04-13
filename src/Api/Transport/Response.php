<?php


namespace Daniesy\Rodels\Api\Transport;


use Daniesy\Rodels\Api\Http\HttpClient;

class Response implements \JsonSerializable
{
    /**
     * Contains the raw response
     *
     * @var string
     */
    private $response_raw;

     /**
     * Contains the raw response
     *
     * @var string
     */
    private $response;

    /**
     * Http code.
     *
     * @var array
     */
    private $headers;

    /**
     * Http code.
     *
     * @var int
     */
    private $http_code;

    /**
     * Create a new response instance
     *
     * @param string $response
     * @param HttpClient $httpClient
     */
    public function __construct($response, array $headers, HttpClient $httpClient)
    {
        $this->response_raw = $response;
        $this->headers = $headers;
        $this->http_code = $httpClient->getHttpCode();

        if (is_string($response) && !!$response) {
            $this->response = $this->decodeString($response);
        }
    }

    /**
     * Decode the string to an array
     *
     * @param string $response
     *
     * @return array
     */
    private function decodeString(string $response): array
    {
        return json_decode($response, true);
    }

    /**
     * Get the response code from the request
     *
     * @return int
     */
    public function getResponseCode()
    {
        return $this->http_code;
    }

    public function isJson(): bool
    {
        if (!isset($this->headers['Content-Type'])) {
            return false;
        }
        $header = $this->headers['Content-Type'];
        return strstr($header, "json");
    }

    /**
     * Return the requested key data
     *
     * @param string $key
     *
     * @return array|null
     */
    public function __get($key)
    {
        return $this->__isset($key) ? $this->response[$key] : null;
    }

    /**
     * Set requested key data
     *
     * @param string $key
     * @param string $value
     */
    public function __set($key, $value)
    {
        if ($this->__isset($key)) {
            $this->response[$key] = $value;
        }
    }

    /**
     * Return if the key is set
     *
     * @param string $key
     *
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->response[$key]);
    }

    public function raw(): string
    {
        return $this->response_raw;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4
     */
    public function jsonSerialize()
    {
        return $this->response;
    }
}
