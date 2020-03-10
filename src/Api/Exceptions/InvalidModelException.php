<?php


namespace Daniesy\Rodels\Api\Exceptions;


use Throwable;

class InvalidModelException extends \Exception
{
    public function __construct($model, $code = 0, Throwable $previous = null)
    {
        parent::__construct("The model {$model} does not exist.", $code, $previous);
    }
}
