<?php

namespace Daniesy\Rodels;

use Daniesy\Rodels\Cache\CacheService;
use Illuminate\Support\Manager;

class CacheManager extends Manager
{
    public function createFileDriver(): CacheService
    {
        return new CacheService;
    }

    /**
     * Get the default cache driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->container['config']['rodels.cache'];
    }
}
