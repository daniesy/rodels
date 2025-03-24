<?php

namespace Daniesy\Rodels\Facade;

use Daniesy\Rodels\Api\Remote as RemoteClient;
use Illuminate\Support\Facades\Facade;

class Remote extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return RemoteClient::class;
    }
}
