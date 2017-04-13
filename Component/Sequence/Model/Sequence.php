<?php

namespace CoreShop\Component\Sequence\Model;

use CoreShop\Component\Resource\Model\SetValuesTrait;

class Sequence implements SequenceInterface
{
    use SetValuesTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $index = 0;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    public function incrementIndex()
    {
        ++$this->index;
    }
}
