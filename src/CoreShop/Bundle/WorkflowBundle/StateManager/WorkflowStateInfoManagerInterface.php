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

namespace CoreShop\Bundle\WorkflowBundle\StateManager;

use Pimcore\Model\DataObject;
use Pimcore\Model\Element\Note;

interface WorkflowStateInfoManagerInterface
{
    /**
     * @return Note[]
     */
    public function getStateHistory(DataObject\Concrete $object): array;

    public function getStateInfo(string $workflowName, string $value, bool $forFrontend = true, ?string $locale = null): array;

    public function parseTransitions(object $subject, string $workflowName, array $transitions = [], bool $forFrontend = true, ?string $locale = null);
}
