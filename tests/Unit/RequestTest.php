<?php

use Daniesy\Rodels\Api\Http\HttpClient;
use Daniesy\Rodels\Api\Transport\Request;
use Daniesy\Rodels\Api\Transport\Response;
use Daniesy\Rodels\Cache\CacheService;

it('returns response for valid request', function () {
    $httpClient = Mockery::mock(HttpClient::class);

    $httpClient->shouldReceive('run')->andReturn(['{"data":"value"}', []]);
    $httpClient->shouldReceive('hasErrors')->andReturn(false);
    $httpClient->shouldReceive('getHttpCode')->andReturn(200);
    $httpClient->shouldReceive('getHeaders')->andReturn([]);

    $request = new Request('https://example.com', $httpClient);
    $result = $request->get('endpoint');

    expect($result)->toBeInstanceOf(Response::class)
        ->and($result->data)->toBe('value');
});

it('caches response for valid request', function () {
    $httpClient = Mockery::mock(HttpClient::class);

    $httpClient->shouldReceive('run')->andReturn(['{"data":"value"}', []]);
    $httpClient->shouldReceive('hasErrors')->andReturn(false);
    $httpClient->shouldReceive('getHttpCode')->andReturn(200);
    $httpClient->shouldReceive('getHeaders')->andReturn([]);

    $cache = new CacheService;

    $request = new Request('https://example.com', $httpClient, $cache);
    $result = $request->get('endpoint');

    expect($result)->toBeInstanceOf(Response::class)
        ->and($result->data)->toBe('value');


    $request = new Request('https://example.com', $httpClient, $cache);
    $result = $request->get('endpoint');

    expect($result->isCached())->toBeTrue();

    $request = new Request('https://example.com', $httpClient, $cache);
    $result = $request->post('endpoint', []);

    expect($result)->toBeInstanceOf(Response::class)
        ->and($result->data)->toBe('value');


    $request = new Request('https://example.com', $httpClient, $cache);
    $result = $request->get('endpoint');
    expect($result->isCached())->toBeFalse();
});
