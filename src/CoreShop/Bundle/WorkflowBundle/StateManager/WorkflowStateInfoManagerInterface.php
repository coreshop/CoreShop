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

namespace CoreShop\Bundle\WorkflowBundle\StateManager;

use Pimcore\Model\DataObject;
use Pimcore\Model\Element\Note;

interface WorkflowStateInfoManagerInterface
{
    /**
     * @param DataObject\Concrete $object
     *
     * @return Note[]
     */
    public function getStateHistory(DataObject\Concrete $object): array;

    public function getStateInfo(string $workflowName, string $value, bool $forFrontend = true): array;

    public function parseTransitions($subject, string $workflowName, array $transitions = [], bool $forFrontend = true);
}
