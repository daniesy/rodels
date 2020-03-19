<?php

namespace Daniesy\Rodels\Api\Transport;

use Daniesy\Rodels\Api\Exceptions\ApiException;
use Daniesy\Rodels\Api\Http\HttpClient;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\UrlGenerator;

class Request
{
    /**
     * Host to make the calls to
     *
     * @var string
     */
    private $host;

    /**
     * Instance of the Http client class
     *
     * @var HttpClient
     */
    private $httpClient;
    /**
     * Array with the headers from the last request
     *
     * @var array
     */
    private $response_headers;

    /**
     * Request constructor.
     * @param string $host
     * @param HttpClient $httpClient
     */
    public function __construct(string $host, HttpClient $httpClient)
    {
        $this->host = $host;

        if (substr($this->host, -1) != "/") {
            $this->host .= "/";
        }

        $this->httpClient = $httpClient;
    }

    /**
     * Add header to request.
     *
     * @param string $key
     * @param string $value
     * @return Request
     */
    public function setHeader($key, $value): self
    {
        $this->httpClient->setHeader($key, $value);
        return $this;
    }

    /**
     * Add headers to request.
     *
     * @param array $headers
     * @return Request
     */
    public function setHeaders(array $headers = []): self
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }

        return $this;
    }

    /**
     * Make a get request to the given endpoint
     *
     * @param string $endpoint
     * @param array $parameters
     *
     * @return Response
     */
    public function get($endpoint, array $parameters = []): Response
    {
        $url = sprintf('%s%s?%s', $this->host, $endpoint, http_build_query($parameters));
        return $this->execute('GET', $url);
    }

    /**
     * Make a post request to the given endpoint
     *
     * @param string $endpoint
     * @param array $parameters
     *
     * @return Response
     */
    public function post($endpoint, array $parameters = []): Response
    {
        return $this->execute('POST', sprintf('%s%s', $this->host, $endpoint), $parameters);
    }

    /**
     * Make a delete request to the given endpoint
     *
     * @param string $endpoint
     * @param array $parameters
     *
     * @return Response
     */
    public function delete($endpoint, array $parameters = []): Response
    {
        return $this->execute('DELETE', sprintf('%s%s', $this->host, $endpoint), $parameters);
    }

    /**
     * Make an put request to the given endpoint
     *
     * @param string $endpoint
     * @param array $parameters
     *
     * @return Response
     */
    public function put($endpoint, array $parameters = []): Response
    {
        return $this->execute('PUT', sprintf('%s%s', $this->host, $endpoint), $parameters);
    }

    /**
     * Make an patch request to the given endpoint
     *
     * @param string $endpoint
     * @param array $parameters
     *
     * @return Response
     */
    public function patch($endpoint, array $parameters = []): Response
    {
        return $this->execute('PATCH', sprintf('%s%s', $this->host, $endpoint), $parameters);
    }

    /**
     * Return the headers from the last request
     *
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->response_headers;
    }

    /**
     * Execute the http request
     *
     * @param string $method
     * @param string $url
     * @param array $parameters
     * @param array $headers
     *
     * @return Response
     *
     * @throws ApiException|HttpResponseException
     */
    public function execute($method, $url, array $parameters = [], $headers = []): Response
    {
        // Execute request and catch response
        list($response_data, $response_headers) = $this->httpClient->run($method, $url, $parameters, $headers);

        // Check if we have a valid response
        if ($this->httpClient->hasErrors()) {
            throw new ApiException($this->httpClient->getHttpCode(), [$this->httpClient->getErrors()]);
        }

        // Initiate the response
        $response = new Response($response_data, $this->httpClient);

        // Check the response code
        if ($response->getResponseCode() >= 400) {
            if ($response->getResponseCode() === 400) {
                $this->throwValidationException($response);
            }
            throw new ApiException($response->getResponseCode(), [$response->error ?? $response->errors]);
        }

        // Set headers for later inspection
        $this->response_headers = $response_headers;

        // Return the response
        return $response;
    }

    private function throwValidationException(Response $response)
    {
        // Get laravel request
        $request = app(\Illuminate\Http\Request::class);

        // Translate errors
        $errors = $response->errors;
        foreach ($errors as $attr => $messages) {
            $errors[$attr] = array_map(function ($error) use ($attr) {
                return trans($error, ['attribute' => $attr]);
            }, $messages);
        }

        // Return response
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

    /**
     * Get the URL we should redirect to.
     *
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return app(UrlGenerator::class)->previous();
    }
}
