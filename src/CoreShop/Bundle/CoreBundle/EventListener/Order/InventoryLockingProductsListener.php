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

namespace CoreShop\Bundle\CoreBundle\EventListener\Order;

use CoreShop\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Workflow\ProposalWorkflowEvent;
use CoreShop\Component\Order\Workflow\WorkflowManagerInterface;

final class InventoryLockingProductsListener
{
    /**
     * @var OrderInventoryOperatorInterface
     */
    private $orderInventoryOperator;

    /**
     * @param OrderInventoryOperatorInterface $orderInventoryOperator
     */
    public function __construct(OrderInventoryOperatorInterface $orderInventoryOperator)
    {
        $this->orderInventoryOperator = $orderInventoryOperator;
    }

    /**
     * @param ProposalWorkflowEvent $event
     */
    public function onOrderChangeEvent(ProposalWorkflowEvent $event)
    {
        $order = $event->getProposal();

        if (!$order instanceof OrderInterface) {
            return;
        }

        try {
            if ($event->getNewState() === WorkflowManagerInterface::ORDER_STATE_INITIALIZED) {
                $this->orderInventoryOperator->hold($order);
            }

            if ($event->getNewState() === WorkflowManagerInterface::ORDER_STATE_PROCESSING) {
                $this->orderInventoryOperator->sell($order);
            }

            if ($event->getNewState() === WorkflowManagerInterface::ORDER_STATE_CANCELED) {
                $this->orderInventoryOperator->cancel($order);
            }
        }
        catch (\InvalidArgumentException $ex) {
            //TODO: Not Sure yet what to do :/
        }
    }
}