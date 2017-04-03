<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ViewHandler implements ViewHandlerInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($data, $options = [])
    {
        $context = SerializationContext::create();
        $context->setSerializeNull(true);

        if (array_key_exists('group', $options)) {
            $context->setGroups(['Default', $options['group']]);
        }

        return new JsonResponse($this->serializer->serialize($data, 'json', $context), 200, [], true);
    }
}
