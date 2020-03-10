<?php

namespace Daniesy\Rodels\Api\Http;

abstract class HttpClient
{
    /**
     * Last request http status.
     *
     * @var int
     **/
    protected $http_code = 200;

    /**
     * Last request error string.
     *
     * @var string
     **/
    protected $errors = null;

    /**
     * Array containing headers from last performed request.
     *
     * @var array
     */
    protected $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    /**
     * Add multiple headers to request.
     *
     * @param array $values
     */
    public function setHeaders(array $values)
    {
        foreach ($values as $key => $value) {
            $this->setHeader($key, $value);
        }
    }

    /**
     * Add header to request.
     *
     * @param string $key
     * @param string $value
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = "{$key}: {$value}";
    }

    /**
     * Parse string headers into array
     *
     * @param string $headers
     *
     * @return array
     */
    protected function parseHeaders(string $headers): array
    {
        $result = [];

        foreach (explode("\n", $headers) as $row) {
            $header = explode(':', $row, 2);

            if (count($header) == 2) {
                $result[$header[0]] = trim($header[1]);
            } else {
                $result[] = $header[0];
            }
        }

        return $result;
    }

    /**
     * Check if the curl request ended up with errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        return is_null($this->errors) === false;
    }

    /**
     * Get curl errors
     *
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get last curl HTTP code.
     *
     * @return int
     */
    public function getHttpCode()
    {
        return $this->http_code;
    }

    /**
     * Execute the request
     *
     * @param  string $method
     * @param  string $url
     * @param  array  $parameters
     * @param  array  $headers
     *
     * @return array
     */
    public abstract function run(string $method, string $url, array $parameters = [], array $headers = []): array;

    public abstract function pack(array $params);

}
