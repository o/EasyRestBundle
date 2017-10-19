<?php


namespace Osm\EasyRestBundle\ParamConverter;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Request        $request
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
