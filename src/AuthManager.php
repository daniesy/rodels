<?php


namespace Daniesy\Rodels;


use Daniesy\Rodels\Auth\KeyAuthenticator;
use Illuminate\Support\Manager;

class AuthManager extends Manager
{

    public function createKeyDriver()
    {
        return new KeyAuthenticator;
    }

    /**
     * Get the default mail driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->container['config']['rodels.auth'];
    }
}
