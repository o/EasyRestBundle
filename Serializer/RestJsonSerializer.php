<?php

namespace Osm\EasyRestBundle\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class RestJsonSerializer
{

    const FORMAT = 'json';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * RestJsonSerializer constructor.
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \RuntimeException
     * @throws \Symfony\Component\Serializer\Exception\RuntimeException
     */
    public function __construct()
    {
        $this->serializer = $this->createSerializer();
    }

    /**
     * @return Serializer
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \RuntimeException
     * @throws \Symfony\Component\Serializer\Exception\RuntimeException
     */
    public function createSerializer()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

        return new Serializer(
            [
                new ArrayDenormalizer(),
                new DateTimeNormalizer(),
                new JsonSerializableNormalizer(),
                new ObjectNormalizer($classMetadataFactory, null, null, new PhpDocExtractor()),
            ],
            [
                new JsonEncoder(),
            ]
        );
    }

    /**
     * @param $data
     * @return bool|float|int|string
     * @throws \Symfony\Component\Serializer\Exception\NotEncodableValueException
     */
    public function serialize($data)
    {
        return $this->serializer->serialize($data, self::FORMAT);
    }

    /**
     * @param $data
     * @param $type
     * @return object
     * @throws \Symfony\Component\Serializer\Exception\NotEncodableValueException
     */
    public function deserialize($data, $type)
    {
        return $this->serializer->deserialize($data, $type, self::FORMAT);
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

}