<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\WorkflowBundle\MarkingStore;

use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

class PimcorePersistentMarkingStore implements MarkingStoreInterface
{
    public function __construct(
        private MarkingStoreInterface $originMarkingStore,
        private bool $persistDirectly = true,
    ) {
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
