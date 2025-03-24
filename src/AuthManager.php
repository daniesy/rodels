<?php


namespace Daniesy\Rodels;


use Daniesy\Rodels\Auth\KeyAuthenticator;
use Illuminate\Support\Manager;

class AuthManager extends Manager
{

    public function createKeyDriver(): KeyAuthenticator
    {
        return new KeyAuthenticator;
    }

    /**
     * Get the default auth driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->container['config']['rodels.auth'];
    }
}
