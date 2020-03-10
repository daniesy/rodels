<?php

namespace Daniesy\Rodels;

use Daniesy\Rodels\Api\Auth\Authenticator;
use Daniesy\Rodels\Api\Remote;
use Daniesy\Rodels\Auth\KeyAuthenticator;
use Daniesy\Rodels\Clients\RemoteApi;
use Daniesy\Rodels\Commands\MakeEndpoint;
use Daniesy\Rodels\Commands\MakeRodel;
use Illuminate\Support\ServiceProvider;

class RodelsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeRodel::class,
                MakeEndpoint::class,
            ]);
        }

        $this->mergeConfigFrom(
            __DIR__.'/Config/rodels.php', 'rodels'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Config/rodels.php' => config_path('rodels.php'),
        ]);

        switch (config('rodels.auth')) {
            case 'key':

        }

        $this->app->bind(Remote::class, function () { return new Remote($this->getAuthenticator()); });
    }

    private function getAuthenticator() :? Authenticator
    {
        switch (config('rodels.auth')) {
            case 'key':
                return new KeyAuthenticator;
        }
        return null;
    }
}
