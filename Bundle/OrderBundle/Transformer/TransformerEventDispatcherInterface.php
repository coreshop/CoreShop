<?php

namespace CoreShop\Bundle\OrderBundle\Transformer;

interface TransformerEventDispatcherInterface
{
    /**
     * @param string $modelName
     * @param string $model
     * @param array $params
     */
    public function dispatchPreEvent($modelName, $model, $params = []);

    /**
     * @param string $modelName
     * @param string $model
     * @param array $params
     */
    public function dispatchPostEvent($modelName, $model, $params = []);
}
