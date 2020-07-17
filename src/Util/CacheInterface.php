<?php

namespace App\Util;

interface CacheInterface
{
    /**
     * Cache
     *
     * @param string $name
     * @param int $time
     * @param array $tags
     * @param callable $calculations
     *
     * @return mixed
     */
    public function cache(string $name, int $time, array $tags, callable $calculations);

    /**
     * Get item
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getItem(string $key);

    /**
     * Delete cache
     *
     * @param string $key
     *
     * @return void
     */
    public function deleteCache(string $key): void;
    
    /**
     * Invalidate cache
     *
     * @param array $tag
     *
     * @return void
     */
    public function invalidateCache(array $tag): void;
}
