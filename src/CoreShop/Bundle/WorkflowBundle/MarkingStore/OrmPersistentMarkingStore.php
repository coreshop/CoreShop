<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\WorkflowBundle\MarkingStore;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

class OrmPersistentMarkingStore implements MarkingStoreInterface
{
    private MarkingStoreInterface $originMarkingStore;
    private Registry $doctrineRegistry;

    public function __construct(MarkingStoreInterface $originMarkingStore, Registry $doctrineRegistry)
    {
        $this->originMarkingStore = $originMarkingStore;
        $this->doctrineRegistry = $doctrineRegistry;
    }

    public function getMarking($subject): Marking
    {
        return $this->originMarkingStore->getMarking($subject);
    }

    public function setMarking($subject, Marking $marking, array $context = []): void
    {
        $this->originMarkingStore->setMarking($subject, $marking);
        $manager = $this->doctrineRegistry->getManagerForClass(get_class($subject));

        $manager->persist($subject);
        $manager->flush();
    }
}
