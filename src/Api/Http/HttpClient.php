<?php

namespace Daniesy\Rodels\Api\Http;

abstract class HttpClient
{
    /**
     * Last request http status.
     *
     * @var int
     **/
    protected int $http_code = 200;

    /**
     * Last request error string.
     *
     * @var string|null
     **/
    protected ?string $errors = null;

    /**
     * Array containing headers from last performed request.
     *
     * @var array
     */
    protected array $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    /**
     * Add multiple headers to request.
     */
    public function setHeaders(array $values): void
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
    public function setHeader(string $key, string $value): void
    {
        $this->headers[$key] = "{$key}: {$value}";
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Parse string headers into array
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
    public function hasErrors(): bool
    {
        return is_null($this->errors) === false;
    }

    /**
     * Get curl errors
     *
     * @return string|null
     */
    public function getErrors(): ?string
    {
        return $this->errors;
    }

    /**
     * Get last curl HTTP code.
     *
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->http_code;
    }

    /**
     * Execute the request
     *
     * @param string $method
     * @param string $url
     * @param array $payload
     * @return array
     */
    abstract public function run(string $method, string $url, array $payload = []): array;

    abstract public function pack(array $params);
}
