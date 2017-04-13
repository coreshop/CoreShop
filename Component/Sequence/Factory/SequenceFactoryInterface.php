<?php

namespace CoreShop\Component\Sequence\Factory;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use coreShop\Component\Sequence\Model\SequenceInterface;

interface SequenceFactoryInterface extends FactoryInterface
{
    /**
     * @param string $type
     * @return SequenceInterface
     */
    public function createWithType($type);

}