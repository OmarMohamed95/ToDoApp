<?php

namespace App\Util;

interface CacheInterface
{

    public function cache($name, $time, $tags, $calculations);

    public function getItem($key);

    public function deleteCache($key);
    
    public function invalidateCache($tag);

}