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

namespace CoreShop\Bundle\WorkflowBundle\Manager;

use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;

final class StateMachineManager implements StateMachineManagerInterface
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function get($subject, string $workflowName = null): Workflow
    {
        return $this->registry->get($subject, $workflowName);
    }

    public function getTransitionFromState(Workflow $workflow, $subject, string $fromState): ?string
    {
        /** @var Transition $transition */
        foreach ($workflow->getEnabledTransitions($subject) as $transition) {
            if (in_array($fromState, $transition->getFroms(), true)) {
                return $transition->getName();
            }
        }

        return null;
    }

    public function getTransitionToState(Workflow $workflow, $subject, string $toState): ?string
    {
        /** @var Transition $transition */
        foreach ($workflow->getEnabledTransitions($subject) as $transition) {
            if (in_array($toState, $transition->getTos(), true)) {
                return $transition->getName();
            }
        }

        return null;
    }
}
