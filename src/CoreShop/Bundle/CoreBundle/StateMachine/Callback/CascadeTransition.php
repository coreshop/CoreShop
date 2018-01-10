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

namespace CoreShop\Bundle\CoreBundle\StateMachine\CallBack;

use CoreShop\Bundle\CoreBundle\StateMachine\StateMachineManager;
use Symfony\Component\Workflow\Event\Event;

class CascadeTransition
{
    /**
     * @var StateMachineManager
     */
    protected $stateMachineManager;

    /**
     * @param StateMachineManager $stateMachineManager
     */
    public function __construct(StateMachineManager $stateMachineManager)
    {
        $this->stateMachineManager = $stateMachineManager;
    }

    /**
     * @param Event $event
     */
    /**
     * @param \Traversable|array $objects
     * @param Event              $event Event
     * @param string|null        $transition
     * @param string|null        $workflowName
     * @param bool               $soft
     */
    public function apply($objects, Event $event, $transition = null, $workflowName = null, $soft = true)
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