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

namespace CoreShop\Bundle\WorkflowBundle\History;

use Pimcore\Model\DataObject;
use Pimcore\Model\Element\Note;

class HistoryRepository implements HistoryRepositoryInterface
{
    public function __construct(
        private string $noteIdentifier,
    ) {
    }

    public function getHistory(DataObject\Concrete $object): array
    {
        $noteList = new Note\Listing();
        $noteList->addConditionParam('type = ?', $this->noteIdentifier);
        $noteList->addConditionParam('cid = ?', $object->getId());
        $noteList->setOrderKey('date');
        $noteList->setOrder('desc');

        return $noteList->load();
    }
}
