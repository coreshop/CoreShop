<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Workflow;

use CoreShop\Component\Order\Model\ProposalInterface;

interface WorkflowStateManagerInterface
{
    /**
     * @param ProposalInterface $proposal
     *
     * @return mixed
     */
    public function getStateHistory(ProposalInterface $proposal);

    /**
     * @param string $workflowName
     * @param string $value
     * @param bool   $forFrontend
     *
     * @return array
     */
    public function getStateInfo($workflowName, $value, $forFrontend = true);

    /**
     * @param mixed  $subject
     * @param string $workflowName
     * @param array  $transitions
     * @param bool   $forFrontend
     *
     * @return mixed
     */
    public function parseTransitions($subject, $workflowName, $transitions = [], $forFrontend = true);
}
