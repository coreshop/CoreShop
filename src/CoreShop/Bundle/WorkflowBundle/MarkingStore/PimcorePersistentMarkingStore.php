<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\WorkflowBundle\MarkingStore;

use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

class PimcorePersistentMarkingStore implements MarkingStoreInterface
{
    public function __construct(private MarkingStoreInterface $originMarkingStore, private bool $persistDirectly = true)
    {
    }

    public function getMarking($subject): Marking
    {
        return $this->originMarkingStore->getMarking($subject);
    }

    public function setMarking(object $subject, Marking $marking, array $context = []): void
    {
        $this->originMarkingStore->setMarking($subject, $marking);

        if ($this->persistDirectly && $subject instanceof Concrete) {
            $subject->save();
        }
    }
}
