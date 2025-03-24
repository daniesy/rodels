<?php

namespace Daniesy\Rodels\Api\Cache;

use Daniesy\Rodels\Api\Transport\Request;
use Daniesy\Rodels\Api\Transport\Response;

interface Store
{
    /**
     * Remember a request and response
     */
    public function remember(string $url, array $headers, Response $response): void;

    /**
     * Get a response from cache
     */
    public function get(string $url, array $headers): ?Response;

    /**
     * Clear a response from cache
     *
     */
    public function clear(): void;
}
