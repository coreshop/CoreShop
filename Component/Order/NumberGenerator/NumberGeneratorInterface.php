<?php

namespace CoreShop\Component\Order\NumberGenerator;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface NumberGeneratorInterface
{
    /**
     * @param ResourceInterface $model
     * @return mixed
     */
    public function generate(ResourceInterface $model);
}