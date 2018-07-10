<?php


namespace Osm\EasyRestBundle\ParamConverter;


use Doctrine\Common\Annotations\AnnotationReader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;


class JsonParamConverter implements ParamConverterInterface
{

    const FORMAT = 'json';

    /**
     * @var SerializerInterface $serializer
     */
    private $serializer;

    /**
     * JsonParamConverter constructor.
     */
    public function __construct()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $serializer = new Serializer(
            [new ArrayDenormalizer(), new ObjectNormalizer($classMetadataFactory, null, null, new PhpDocExtractor())],
            [new JsonEncoder()]
        );
        $this->serializer = $serializer;
    }


    /**
     * @param Request $request
     * @param ParamConverter $configuration
     * @return void
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $class = $configuration->getClass();

        $object = $this->serializer->deserialize(
            $request->getContent(),
            $class,
            self::FORMAT
        );

        $request->attributes->set($configuration->getName(), $object);
    }

    /**
     * @param ParamConverter $configuration
     * @return bool
     */
    public function supports(ParamConverter $configuration)
    {
        return is_subclass_of($configuration->getClass(), JsonRequestInterface::class);
    }

}
