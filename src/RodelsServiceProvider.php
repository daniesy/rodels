<?php

namespace Daniesy\Rodels;

use Daniesy\Rodels\Api\Remote;
use Daniesy\Rodels\Commands\MakeEndpoint;
use Daniesy\Rodels\Commands\MakeRodel;
use Illuminate\Support\ServiceProvider;

class RodelsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerAuthenticator();
        $this->registerCache();
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
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/Config/rodels.php' => config_path('rodels.php'),
        ]);

        $this->app->bind(Remote::class, function () {
            return new Remote(
                $this->app['rodels.auth']->driver()
            );
        });

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
    }

    private function registerAuthenticator(): void
    {
        $this->app->singleton('rodels.auth', function () {
            return new AuthManager($this->app);
        });
    }

    private function registerCache(): void
    {
        $this->app->singleton('rodels.cache', function () {
            return new CacheManager($this->app);
        });
    }
}
