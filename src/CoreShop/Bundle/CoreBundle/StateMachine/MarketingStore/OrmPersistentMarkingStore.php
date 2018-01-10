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

namespace CoreShop\Bundle\CoreBundle\StateMachine\MarketingStore;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

class OrmPersistentMarkingStore implements MarkingStoreInterface
{
    /**
     * Origin marking store
     *
     * @var MarkingStoreInterface
     */
    private $originMarkingStore;

    /**
     * Doctrine registry
     *
     * @var Registry
     */
    private $doctrineRegistry;

    /**
     * OrmPersistentMarkingStore constructor.
     *
     * @param MarkingStoreInterface $originMarkingStore origin marking store
     * @param Registry              $doctrineRegistry doctrine registry
     */
    public function __construct(MarkingStoreInterface $originMarkingStore, Registry $doctrineRegistry)
    {
        $this->originMarkingStore = $originMarkingStore;
        $this->doctrineRegistry = $doctrineRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getMarking($subject)
    {
        return $this->originMarkingStore->getMarking($subject);
    }

    /**
     * {@inheritdoc}
     */
    public function setMarking($subject, Marking $marking)
    {
        $this->originMarkingStore->setMarking($subject, $marking);
        $manager = $this->doctrineRegistry->getManagerForClass(get_class($subject));
        $manager->persist($subject);
        $manager->flush();
    }
}
