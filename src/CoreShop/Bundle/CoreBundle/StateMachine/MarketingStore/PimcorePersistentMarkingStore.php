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
     * OrmPersistentMarkingStore constructor.
     *
     * @param MarkingStoreInterface $originMarkingStore origin marking store
     */
    public function __construct(MarkingStoreInterface $originMarkingStore)
    {
        $this->originMarkingStore = $originMarkingStore;
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
        if($subject instanceof Concrete) {
            $subject->save();
        }
    }
}
