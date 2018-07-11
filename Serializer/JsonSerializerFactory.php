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

class JsonSerializerFactory
{

    const FORMAT = 'json';

    public static function factory()
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

}