<?php

namespace App\Service\Serializer;

/**
 * CircularReferenceHandler
 */
class CircularReferenceHandler
{
    public function __invoke($object)
    {
        return $object->getId();
    }
}