<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Util\CacheInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
// use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

/**
 * RedisCache
 */
class RedisCache extends AbstractController implements CacheInterface
{
    /** @var RedisAdapter */
    private $client;

    /** @var TagAwareAdapter */
    private $cache;

    public function __construct()
    {
        // $this->client = new \Predis\Client();
        
        $this->client = RedisAdapter::createConnection(
            
            // provide a string dsn
            'redis://localhost:6379',
            // associative array of configuration options
            [
                'compression' => true,
                'lazy' => false,
                'persistent' => 0,
                'persistent_id' => null,
                'tcp_keepalive' => 0,
                'timeout' => 30,
                'read_timeout' => 0,
                'retry_interval' => 0,
                'class' => '\Predis\Client'
            ]
        );

        $RedisAdapter = new RedisAdapter($this->client);
        
        $this->cache = new TagAwareAdapter($RedisAdapter);
    }

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
    public function cache(string $name, int $time, array $tags, callable $calculations)
    {
        $cachedValue = $this->cache->get(
            $name,
            function (ItemInterface $itemInterface) use ($time, $tags, $calculations) {
                $itemInterface->expiresAfter($time);
                $itemInterface->tag($tags);

                if (is_callable($calculations)) {
                    return call_user_func($calculations);
                }
            }
        );

        return $cachedValue;
    }

    /**
     * Get item
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getItem(string $key)
    {
        return $this->cache->getItem($key);
    }

    /**
     * Delete cache
     *
     * @param string $key
     *
     * @return void
     */
    public function deleteCache(string $key): void
    {
        $this->cache->delete($key);
    }

    /**
     * Invalidate cache
     *
     * @param array $tag
     *
     * @return void
     */
    public function invalidateCache(array $tag): void
    {
        $this->cache->invalidateTags($tag);
    }
}
