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
        $this->registerAuthenticator();
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

        $this->app->bind(Remote::class, function () { 
            return new Remote(
                $this->app['rodels.auth']->driver()
            ); 
        });
    }

    private function registerAuthenticator()
    {
        $this->app->singleton('rodels.auth', function() {
            return new AuthManager($this->app);
        });
    }
}
