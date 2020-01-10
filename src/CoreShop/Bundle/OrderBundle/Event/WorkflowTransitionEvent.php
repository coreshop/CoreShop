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

namespace CoreShop\Bundle\OrderBundle\Event;

use Symfony\Component\EventDispatcher\Event;

final class WorkflowTransitionEvent extends Event
{
    /**
     * @var array
     */
    protected $allowedTransitions;

    /**
     * @var string
     */
    protected $workflowName;

    /**
     * @param array  $allowedTransitions
     * @param string $workflowName
     */
    public function __construct(array $allowedTransitions, $workflowName)
    {
        $this->allowedTransitions = $allowedTransitions;
        $this->workflowName = $workflowName;
    }

    /**
     * @return string
     */
    public function getWorkflowName()
    {
        return $this->workflowName;
    }

    /**
     * @param array $allowedTransitions
     */
    public function addAllowedTransitions(array $allowedTransitions)
    {
        $this->allowedTransitions = array_merge($this->allowedTransitions, $allowedTransitions);
    }

    /**
     * @param array $allowedTransitions
     */
    public function setAllowedTransitions(array $allowedTransitions)
    {
        $this->allowedTransitions = $allowedTransitions;
    }

    /**
     * @return array
     */
    public function getAllowedTransitions()
    {
        return $this->allowedTransitions;
    }
}
