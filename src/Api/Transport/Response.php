<?php

namespace Daniesy\Rodels\Api\Transport;

use Daniesy\Rodels\Api\Http\HttpClient;

class Response implements \JsonSerializable
{
    /**
     * Contains the raw response
     */
    private string $response_raw;

    /**
     * Contains the decoded response
     */
    private string|array $response;

    /**
     * Http headers.
     */
    private array $headers;

    /**
     * Http code.
     */
    private int $http_code;
    private bool $isCached = false;

    /**
     * Create a new response instance
     */
    public function __construct(string $response, array $headers, ?HttpClient $httpClient = null, bool $isCached = false)
    {
        $this->response_raw = $response;
        $this->headers = $headers;
        $this->http_code = $httpClient?->getHttpCode() ?? 0;

        $this->response = $this->decodeString($response);
        $this->isCached = $isCached;
    }

    /**
     * Decode the string to an array
     */
    private function decodeString(string $response): array|string
    {
        $decoded = json_decode($response, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $response;
    }

    /**
     * Get the response code from the request
     */
    public function getResponseCode(): int
    {
        return $this->http_code;
    }

    public function isJson(): bool
    {
        return isset($this->headers['Content-Type']) && str_contains($this->headers['Content-Type'], 'json');
    }

    public function isCached(): bool
    {
        return $this->isCached;
    }

    /**
     * Return the requested key data
     */
    public function __get(string $key): mixed
    {
        return $this->response[$key] ?? null;
    }

    /**
     * Set requested key data
     */
    public function __set(string $key, mixed $value): void
    {
        $this->response[$key] = $value;
    }

    /**
     * Return if the key is set
     */
    public function __isset(string $key): bool
    {
        return isset($this->response[$key]);
    }

    public function raw(): string
    {
        return $this->response_raw;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return string|array data which can be serialized by <b>json_encode</b>,
     *                      which is a value of any type other than a resource.
     */
    public function jsonSerialize(): string|array
    {
        return $this->response;
    }

    public function headers(): array
    {
        return $this->headers;
    }
}
