<?php

namespace Daniesy\Rodels\Cache;

use Daniesy\Rodels\Api\Cache\Store;
use Daniesy\Rodels\Api\Transport\Response;
use Illuminate\Database\RecordNotFoundException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CacheService implements Store
{
    private string $table;

    public function __construct()
    {
        $this->table = Config::get('rodels.cache.table');
    }

    public function clear(): void
    {
        DB::table($this->table)->truncate();
    }

    public function remember(string $url, array $headers, Response $response): void
    {
        DB::table($this->table)->updateOrInsert(
            $this->buildCacheKey($url, $headers),
            [
                'responseRaw' => $response->raw(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')]
        );
    }

    public function get(string $url, array $headers): ?Response
    {
        try {
            $cached = DB::table($this->table)
                ->where($this->buildCacheKey($url, $headers))
                ->where('updated_at', '>', $this->generateCacheTime())
                ->firstOrFail();

            return new Response($cached->responseRaw, [], null, true);
        } catch (RecordNotFoundException $exception) {
            return null;
        }
    }

    private function buildCacheKey(string $url, array $headers): array
    {
        return [
            'url' => $url,
            'headers' => json_encode($headers),
        ];
    }

    private function generateCacheTime(): string
    {
        $ttl = Config::get('rodels.cache.ttl', 300); // Default to 5 minutes if not set
        
        if ($ttl <= 0) {
            return date('Y-m-d H:i:s', strtotime('-1 second')); // Cache is considered expired
        }
        
        return date('Y-m-d H:i:s', strtotime("-{$ttl} seconds"));
    }
}