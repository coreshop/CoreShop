<?php

namespace CoreShop\Bundle\CoreBundle\Order\Transformer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class TransformerEventDispatcher implements TransformerEventDispatcherInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchPreEvent($modelName, $model, $params = [])
    {
        $event = $this->getEvent($model, $params);

        $this->eventDispatcher->dispatch(
            sprintf('%s.%s.pre_%s', 'coreshop', $modelName, 'transform'),
            $event
        );
    }


    /**
     * {@inheritdoc}
     */
    public function dispatchPostEvent($modelName, $model, $params = [])
    {
        $event = $this->getEvent($model, $params);

        $this->eventDispatcher->dispatch(
            sprintf('%s.%s.post_%s', 'coreshop', $modelName, 'transform'),
            $event
        );
    }

    /**
     * @param $model
     * @param $params
     * @return GenericEvent
     */
    private function getEvent($model, $params)
    {
        return new GenericEvent($model, $params);
    }
}
