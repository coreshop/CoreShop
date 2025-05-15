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

namespace CoreShop\Bundle\WorkflowBundle\Callback;

use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;
use Symfony\Component\Workflow\Event\Event;

class CascadeTransition
{
    public function __construct(
        protected StateMachineManagerInterface $stateMachineManager,
    ) {
    }

    public function apply($objects, Event $event, $transition = null, $workflowName = null, $soft = true): void
    {
        if (!is_array($objects) && !$objects instanceof \Traversable) {
            $objects = [$objects];
        }
        if (null === $transition) {
            $transition = $event->getTransition();
        }
        if (null === $workflowName) {
            $workflowName = $event->getWorkflowName();
        }
        foreach ($objects as $object) {
            $workflow = $this->stateMachineManager->get($object, $workflowName);
            if ($soft === true) {
                if (!$workflow->can($object, $transition)) {
                    continue;
                }
            }

            $workflow->apply($object, $transition);
        }
    }
}
