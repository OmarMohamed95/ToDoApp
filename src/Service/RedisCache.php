<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Util\CacheInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
// use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

class RedisCache extends AbstractController implements CacheInterface
{
    private $client;
    private $time;
    private $tags;
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

        // Redis Adapter
        $RedisAdapter = new RedisAdapter($this->client);
        
        $this->cache = new TagAwareAdapter($RedisAdapter);
    }

    public function cache($name, $time, $tags, $calculations)
    {       
        $cachedValue = $this->cache->get($name, function(ItemInterface $itemInterface) use ($time, $tags, $calculations){

            $itemInterface->expiresAfter($time);
            $itemInterface->tag($tags);

            if(is_callable($calculations))
            return call_user_func($calculations);

        });

        return $cachedValue;
    }

    public function deleteCache($key)
    {
        $this->cache->delete($key);
    }

    public function invalidateCache($tag)
    {
        $this->cache->invalidateTags($tag);
    }
        
    // public function fileSystem()
    // {
    //     // Adapter for cached items
    //     $this->FilesystemAdapter = new FilesystemAdapter();
    // }
}