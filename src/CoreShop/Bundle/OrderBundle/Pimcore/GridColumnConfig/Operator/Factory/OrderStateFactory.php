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

namespace CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\Operator\Factory;

use CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\Operator\OrderState;
use CoreShop\Component\Order\Workflow\WorkflowStateManagerInterface;
use Pimcore\DataObject\GridColumnConfig\Operator\Factory\OperatorFactoryInterface;
use Pimcore\DataObject\GridColumnConfig\Operator\OperatorInterface;

class OrderStateFactory implements OperatorFactoryInterface
{
    /**
     * @var WorkflowStateManagerInterface
     */
    private $workflowManager;

    /**
     * @param WorkflowStateManagerInterface $workflowManager
     */
    public function __construct(WorkflowStateManagerInterface $workflowManager)
    {
        $this->workflowManager = $workflowManager;
    }

    /**
     * @param \stdClass $configElement
     * @param null      $context
     *
     * @return OperatorInterface
     */
    public function build(\stdClass $configElement, $context = null): OperatorInterface
    {
        return new OrderState($this->workflowManager, $configElement, $context);
    }
}
