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

namespace CoreShop\Bundle\OrderBundle\Workflow;

use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Workflow\WorkflowStateManagerInterface;

/**
 * @deprecated CoreShop\Bundle\OrderBundle\Workflow\WorkflowStateManager is deprecated and will be removed with 2.1, please use \CoreShop\Bundle\WorkflowBundle\StateManager\WorkflowStateInfoManagerInterface instead
 */
final class WorkflowStateManager implements WorkflowStateManagerInterface
{
    /**
     * @var \CoreShop\Bundle\WorkflowBundle\StateManager\WorkflowStateInfoManagerInterface
     */
    private $stateManager;

    /**
     * @param \CoreShop\Bundle\WorkflowBundle\StateManager\WorkflowStateInfoManagerInterface $stateManager
     */
    public function __construct(
        \CoreShop\Bundle\WorkflowBundle\StateManager\WorkflowStateInfoManagerInterface $stateManager
    ) {
        $this->stateManager = $stateManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getStateInfo($workflowName, $value, $forFrontend = true)
    {
        trigger_error(
            sprintf(
                '%s::%s is deprecated and will be removed with 2.1, please use %s:%s instead.',
                static::class,
                __METHOD__,
                \CoreShop\Bundle\WorkflowBundle\StateManager\WorkflowStateInfoManagerInterface::class,
                __METHOD__
            ),
            E_USER_DEPRECATED
        );

        return $this->stateManager->getStateInfo($workflowName, $value, $forFrontend);
    }

    /**
     * {@inheritdoc}
     */
    public function parseTransitions($subject, $workflowName, $transitions = [], $forFrontend = true)
    {
        trigger_error(
            sprintf(
                '%s::%s is deprecated and will be removed with 2.1, please use %s:%s instead.',
                static::class,
                __METHOD__,
                \CoreShop\Bundle\WorkflowBundle\StateManager\WorkflowStateInfoManagerInterface::class,
                __METHOD__
            ),
            E_USER_DEPRECATED
        );

        return $this->stateManager->parseTransitions($subject, $workflowName, $transitions, $forFrontend);
    }

    /**
     * {@inheritdoc}
     */
    public function getStateHistory(ProposalInterface $proposal)
    {
        trigger_error(
            sprintf(
                '%s::%s is deprecated and will be removed with 2.1, please use %s:%s instead.',
                static::class,
                __METHOD__,
                \CoreShop\Bundle\WorkflowBundle\StateManager\WorkflowStateInfoManagerInterface::class,
                __METHOD__
            ),
            E_USER_DEPRECATED
        );

        return $this->stateManager->getStateHistory($proposal);
    }
}
