<?php


namespace Osm\EasyRestBundle\ParamConverter;


use Osm\EasyRestBundle\Serializer\RestJsonSerializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;


class JsonParamConverter implements ParamConverterInterface
{

    const FORMAT = 'json';

    /**
     * @var RestJsonSerializer $serializer
     */
    private $serializer;

    /**
     * JsonParamConverter constructor.
     * @param RestJsonSerializer $serializer
     */
    public function __construct(RestJsonSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Request        $request
     * @param ParamConverter $configuration
     * @return void
     * @throws \LogicException
     * @throws \Symfony\Component\Serializer\Exception\NotEncodableValueException
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $class = $configuration->getClass();

        $object = $this->serializer->deserialize(
            ($request->getContent() !== '') ? $request->getContent() : '{}',
            $class
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
