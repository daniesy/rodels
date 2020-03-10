<?php

namespace Daniesy\Rodels\Api\Auth;

use Daniesy\Rodels\Api\Transport\Request;

interface Authenticator
{
    /**
     * Ads authentication to a request
     *
     * @param Request $request
     * @return Request
     */
    public function addAuth(Request $request): Request;
}
