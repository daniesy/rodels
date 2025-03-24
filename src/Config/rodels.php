<?php

return [
    /*
    |--------------------------------------------------------------------------
    | The remote API host
    |--------------------------------------------------------------------------
    |
    | Set this value to the url of the remote API you want to connect to
    |
    */

    'host' => env('RODELS_HOST'),

    /*
    |--------------------------------------------------------------------------
    | The HTTP client used to send HTTP requests
    |--------------------------------------------------------------------------
    |
    | This option defines the http client that rodels will be using to connect
    | to the remote API.
    |
    | Supported: "curl"
    */

    'client' => 'curl',

    /*
    |--------------------------------------------------------------------------
    | The authentication method
    |--------------------------------------------------------------------------
    |
    | You can set the authentication method that will be used when connecting
    | to the API.
    |
    | Supported: "key"
    */

    'auth' => 'key',

    /*
    |--------------------------------------------------------------------------
    | The connection timeout
    |--------------------------------------------------------------------------
    |
    | The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
    |
    */

    'connection_timeout' => 90,

    'cache' => [
        /*
        |--------------------------------------------------------------------------
        | The cache method
        |--------------------------------------------------------------------------
        |
        | You can set the cache method that will be used to cache responses from the API.
        |
        | Supported: "file", "database", "redis", "null"
        */
        'table' => env('RODELS_CACHE_TABLE', 'rodels_cache'),

        /*
        |--------------------------------------------------------------------------
        | Cache Time-To-Live (TTL)
        |--------------------------------------------------------------------------
        |
        | The number of seconds to cache the API responses.
        |
        */
        'ttl' => env('RODELS_CACHE_TTL', 3600),
    ],

    /*
     |--------------------------------------------------------------------------
     | Authentication configuration
     |--------------------------------------------------------------------------
     |
     | Configure the key authentication method.
     |
     */

    'key' => [
        'name' => 'api-key',
        'value' => env('RODELS_KEY'),
    ],
];
