<?php

namespace Daniesy\Rodels\Api\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiException extends HttpException
{
    public function __construct(int $statusCode, array $messages = [], \Throwable $previous = null, array $headers = [], ?int $code = 0)
    {
        $statusCode = ($statusCode >= 100 && $statusCode < 600) ? $statusCode : 500;
        parent::__construct($statusCode, json_encode($messages), $previous, $headers, $code);
    }
}
