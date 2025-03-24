<?php

namespace Daniesy\Rodels\Api\Transport;

use Daniesy\Rodels\Api\Exceptions\ApiException;
use Daniesy\Rodels\Api\Http\HttpClient;
use Daniesy\Rodels\Cache\CacheService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\UrlGenerator;

class Request
{
    private string $host;

    private HttpClient $httpClient;

    private ?CacheService $cache;

    public function __construct(string $host, HttpClient $httpClient, ?CacheService $cache = null)
    {
        $this->host = rtrim($host, '/').'/';
        $this->httpClient = $httpClient;
        $this->cache = $cache;
    }

    public function setHeader(string $key, string $value): self
    {
        $this->httpClient->setHeader($key, $value);

        return $this;
    }

    public function setHeaders(array $headers = []): self
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }

        return $this;
    }

    public function get(string $endpoint, array $parameters = [], bool $preventCache = false): Response
    {
        return $this->execute(
            method: 'GET',
            endpoint: ! empty($parameters) ? $endpoint : $endpoint.'?'.http_build_query($parameters),
            preventCache: $preventCache
        );
    }

    public function post(string $endpoint, array $payload = [], bool $preventCache = false): Response
    {
        return $this->execute(
            method: 'POST',
            endpoint: $endpoint,
            payload: $payload,
            preventCache: $preventCache
        );
    }

    public function put(string $endpoint, array $payload = [], bool $preventCache = false): Response
    {
        return $this->execute(
            method: 'PUT',
            endpoint: $endpoint,
            payload: $payload,
            preventCache: $preventCache
        );
    }

    public function patch(string $endpoint, array $payload = [], bool $preventCache = false): Response
    {
        return $this->execute(
            method: 'PATCH',
            endpoint: $endpoint,
            payload: $payload,
            preventCache: $preventCache
        );
    }

    public function delete(string $endpoint, array $parameters = [], bool $preventCache = false): Response
    {
        return $this->execute(
            method: 'DELETE',
            endpoint: ! empty($parameters) ? $endpoint : $endpoint.'?'.http_build_query($parameters),
            preventCache: $preventCache
        );
    }

    private function shouldCache(string $method, bool $preventCache): bool
    {
        return in_array($method, ['GET', 'HEAD']) && ! $preventCache;
    }

    private function shouldClearCache(string $method): bool
    {
        return ! in_array($method, ['GET', 'HEAD']);
    }

    private function execute(string $method, string $endpoint, array $payload = [], bool $preventCache = false): Response
    {
        $url = $this->host.$endpoint;
        if ($this->shouldCache($method, $preventCache) && $cachedResponse = $this->cache?->get($url, $this->httpClient->getHeaders())) {
            return $cachedResponse;
        }

        if ($this->shouldClearCache($method)) {
            $this->cache?->clear();
        }

        [$response_data, $response_headers] = $this->httpClient->run($method, $url, $payload);

        if ($this->httpClient->hasErrors()) {
            throw new ApiException($this->httpClient->getHttpCode(), [$this->httpClient->getErrors()]);
        }

        $response = new Response($response_data, $response_headers, $this->httpClient);

        if ($response->getResponseCode() >= 400) {
            if ($response->getResponseCode() === 400) {
                $this->throwValidationException($response);
            }
            throw new ApiException($response->getResponseCode(), [$response->error ?? $response->errors]);
        }

        if ($this->shouldCache($method, $preventCache)) {
            $this->cache?->remember($url, $this->httpClient->getHeaders(), $response);
        }

        return $response;
    }

    private function throwValidationException(Response $response): void
    {
        $request = app(\Illuminate\Http\Request::class);
        $errors = $response->errors;

        foreach ($errors as $attr => $messages) {
            $errors[$attr] = array_map(fn ($error) => trans($error, ['attribute' => $attr]), $messages);
        }

        if ($request->ajax() || $request->wantsJson()) {
            $response = new JsonResponse([
                'message' => trans('validation.validation_failed'),
                'errors' => $errors,
            ], 400);
        } else {
            $response = redirect()->to($this->getRedirectUrl())
                ->withInput($request->input())
                ->withErrors($errors);
        }

        throw new HttpResponseException($response);
    }

    protected function getRedirectUrl(): string
    {
        return app(UrlGenerator::class)->previous();
    }
}
