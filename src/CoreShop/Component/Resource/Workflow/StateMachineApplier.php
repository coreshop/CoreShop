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

namespace CoreShop\Component\Resource\Workflow;

final class StateMachineApplier
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
     * @param      $subject
     * @param null $workflowName
     * @param null $transition
     * @param bool $soft
     */
    public function apply($subject, $workflowName = null, $transition = null, $soft = true)
    {
        $workflow = $this->stateMachineManager->get($subject, $workflowName);
        if ($soft === true) {
            if (!$workflow->can($subject, $transition)) {
                return;
            }
        }
        $workflow->apply($subject, $transition);
    }
}
