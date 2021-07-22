<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\WorkflowBundle\Manager;

use Symfony\Component\Workflow\Workflow;

interface StateMachineManagerInterface
{
    /**
     * @param mixed $subject
     * @param null  $workflowName
     *
     * @return Workflow
     */
    public function get($subject, $workflowName = null);

    /**
     * @param Workflow $workflow
     * @param mixed    $subject
     * @param string   $fromState
     *
     * @return mixed
     */
    public function getTransitionFromState(Workflow $workflow, $subject, string $fromState);

    /**
     * @param Workflow $workflow
     * @param mixed    $subject
     * @param string   $toState
     *
     * @return mixed
     */
    public function getTransitionToState(Workflow $workflow, $subject, string $toState);
}
