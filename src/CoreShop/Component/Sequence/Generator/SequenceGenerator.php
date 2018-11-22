<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Sequence\Generator;

use CoreShop\Component\Sequence\Factory\SequenceFactoryInterface;
use CoreShop\Component\Sequence\Repository\SequenceRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param SequenceRepositoryInterface $sequenceRepository
     * @param SequenceFactoryInterface    $sequenceFactory
     * @param EntityManagerInterface      $entityManager
     */
    public function __construct(SequenceRepositoryInterface $sequenceRepository, SequenceFactoryInterface $sequenceFactory, EntityManagerInterface $entityManager)
    {
        $this->sequenceRepository = $sequenceRepository;
        $this->sequenceFactory = $sequenceFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextSequenceForType($type)
    {
        $sequence = $this->getSequence($type);
        $sequence->incrementIndex();

        $this->entityManager->persist($sequence);
        $this->entityManager->flush();

        return $sequence->getIndex();
    }

    /**
     * @param string $type
     *
     * @return \CoreShop\Component\Sequence\Model\SequenceInterface
     */
    private function getSequence($type)
    {
        $sequence = $this->sequenceRepository->findForType($type);

        if (null === $sequence) {
            $sequence = $this->sequenceFactory->createWithType($type);
            $this->sequenceRepository->add($sequence);
        }

        return $sequence;
    }
}
