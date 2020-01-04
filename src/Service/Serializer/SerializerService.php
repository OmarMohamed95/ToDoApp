<?php

namespace App\Service\Serializer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class SerializerService
{
    private $normalizer;
    private $serializer;

    public function init()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];

        $this->normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        
        return $this->serializer = new Serializer([$this->normalizer], [$encoders]);
        
        // $this->normalizers = [new ObjectNormalizer()];

        // return $this->serializer = new Serializer($this->normalizers, $encoders);
    }
    
}