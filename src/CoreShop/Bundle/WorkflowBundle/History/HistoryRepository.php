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

namespace CoreShop\Bundle\WorkflowBundle\History;

use Pimcore\Model\DataObject;
use Pimcore\Model\Element\Note;

class HistoryRepository implements HistoryRepositoryInterface
{
    /**
     * @var string
     */
    private $noteIdentifier;

    /**
     * @param string $noteIdentifier
     */
    public function __construct(string $noteIdentifier)
    {
        $this->noteIdentifier = $noteIdentifier;
    }

    /**
     * {@inheritdoc}
     */
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
