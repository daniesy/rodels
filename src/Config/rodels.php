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
        'value' => env('RODELS_KEY')
    ],
];
