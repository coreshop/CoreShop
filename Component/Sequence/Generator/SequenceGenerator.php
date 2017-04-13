<?php

namespace CoreShop\Component\Sequence\Generator;

use CoreShop\Component\Sequence\Factory\SequenceFactoryInterface;
use CoreShop\Component\Sequence\Repository\SequenceRepositoryInterface;

class SequenceGenerator implements SequenceGeneratorInterface
{
    /**
     * @var SequenceRepositoryInterface
     */
    private $sequenceRepository;

    /**
     * @var SequenceFactoryInterface
     */
    private $sequenceFactory;

    /**
     * @param SequenceRepositoryInterface $sequenceRepository
     * @param SequenceFactoryInterface $sequenceFactory
     */
    public function __construct(SequenceRepositoryInterface $sequenceRepository, SequenceFactoryInterface $sequenceFactory)
    {
        $this->sequenceRepository = $sequenceRepository;
        $this->sequenceFactory = $sequenceFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextSequenceForType($type)
    {
        $sequence = $this->getSequence($type);
        $sequence->incrementIndex();

        return $sequence->getIndex();
    }

    /**
     * @param $type
     * @return \coreShop\Component\Sequence\Model\SequenceInterface
     */
    private function getSequence($type) {
        $sequence = $this->sequenceRepository->findForType($type);

        if (null === $sequence) {
            $sequence = $this->sequenceFactory->createWithType($type);
            $this->sequenceRepository->add($sequence);
        }

        return $sequence;
    }
}