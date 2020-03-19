<?php

namespace Daniesy\Rodels\Api\Components;

use Daniesy\Rodels\Api\Auth\Authenticator;
use Daniesy\Rodels\Api\Transport\Request;

abstract class Endpoint
{
    /**
     * Instance of the request class
     *
     * @var Request
     */
    protected $request;
    /**
     * @var Authenticator
     */
    private $authenticator;

    /**
     * Create a new model instance
     *
     * @param Request $request
     * @param Authenticator $authenticator
     */
    public function __construct(Request $request, Authenticator $authenticator = null)
    {
        $this->request = $request;
        $this->authenticator = $authenticator;
    }

    public function authRequest(array $headers = []): Request
    {
        if (!$this->authenticator) {
            return $this->request->setHeaders($headers);
        }

        return $this->authenticator->addAuth(clone $this->request->setHeaders($headers));
    }
}
