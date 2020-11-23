<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\WorkflowBundle\MarkingStore;

use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

class PimcorePersistentMarkingStore implements MarkingStoreInterface
{
    private $originMarkingStore;
    private $persistDirectly;

    public function __construct(MarkingStoreInterface $originMarkingStore, bool $persistDirectly = true)
    {
        $this->originMarkingStore = $originMarkingStore;
        $this->persistDirectly = $persistDirectly;
    }

    /**
     * {@inheritdoc}
     */
    public function getMarking($subject): Marking
    {
        return $this->originMarkingStore->getMarking($subject);
    }

    /**
     * {@inheritdoc}
     */
    public function setMarking(object $subject, Marking $marking, array $context = [])
    {
        $this->originMarkingStore->setMarking($subject, $marking);

        if ($this->persistDirectly) {
            if ($subject instanceof Concrete) {
                $subject->save();
            }
        }
    }
}
