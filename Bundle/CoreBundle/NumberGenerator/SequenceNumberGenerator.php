<?php

namespace CoreShop\Bundle\CoreBundle\NumberGenerator;

use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Sequence\Generator\SequenceGeneratorInterface;

class SequenceNumberGenerator implements NumberGeneratorInterface
{
    /**
     * @var SequenceGeneratorInterface
     */
    protected $sequenceNumberGenerator;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param SequenceGeneratorInterface $sequenceNumberGenerator
     * @param string $type
     */
    public function __construct(SequenceGeneratorInterface $sequenceNumberGenerator, $type)
    {
        $this->sequenceNumberGenerator = $sequenceNumberGenerator;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ResourceInterface $model)
    {
        return $this->sequenceNumberGenerator->getNextSequenceForType($this->type);
    }
}