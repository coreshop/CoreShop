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

namespace CoreShop\Bundle\WorkflowBundle\MarkingStore;

use Doctrine\Common\Persistence\ObjectManager;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

class PimcorePersistentMarkingStore implements MarkingStoreInterface
{
    /**
     * Origin marking store
     *
     * @var MarkingStoreInterface
     */
    private $originMarkingStore;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param MarkingStoreInterface $originMarkingStore origin marking store
     * @param ObjectManager $objectManager
     */
    public function __construct(
        MarkingStoreInterface $originMarkingStore,
        ObjectManager $objectManager
    )
    {
        $this->originMarkingStore = $originMarkingStore;
        $this->objectManager = $objectManager;
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

        if ($subject instanceof Concrete) {
            $this->objectManager->persist($subject);
        }
    }
}
