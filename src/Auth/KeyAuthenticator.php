<?php


namespace Daniesy\Rodels\Auth;


use Daniesy\Rodels\Api\Auth\Authenticator;
use Daniesy\Rodels\Api\Transport\Request;

class KeyAuthenticator implements Authenticator
{

    public function addAuth(Request $request): Request
    {
        return $request->setHeader(config('rodels.key.name'), config('rodels.key.value'));

    }
}
