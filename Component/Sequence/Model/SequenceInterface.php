<?php

namespace CoreShop\Component\Sequence\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface SequenceInterface extends ResourceInterface
{
    /**
     * @return int
     */
    public function getIndex();

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @param $type
     */
    public function setType($type);

    public function incrementIndex();
}
