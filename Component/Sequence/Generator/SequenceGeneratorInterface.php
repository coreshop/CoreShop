<?php

namespace CoreShop\Component\Sequence\Generator;

interface SequenceGeneratorInterface
{
    /**
     * @param string $type
     * @return int
     */
    public function getNextSequenceForType($type);
}