<?php

namespace CoreShop\Component\Sequence\Factory;

use CoreShop\Component\Resource\Exception\UnsupportedMethodException;

class SequenceFactory implements SequenceFactoryInterface
{
     /**
     * @var string
     */
    private $className;

    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnsupportedMethodException
     */
    public function createNew()
    {
        throw new UnsupportedMethodException('createNew');
    }

    /**
     * {@inheritdoc}
     */
    public function createWithType($type)
    {
        $sequence = new $this->className();
        $sequence->setType($type);

        return $sequence;
    }
}