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

namespace CoreShop\Bundle\OrderBundle\Templating\Helper;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\OrderPaymentStates;
use CoreShop\Component\Order\OrderShipmentStates;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Order\Workflow\WorkflowStateManagerInterface;
use Symfony\Component\Templating\Helper\Helper;

class OrderStateHelper extends Helper implements OrderStateHelperInterface
{
    /**
     * @var WorkflowStateManagerInterface
     */
    private $workflowStateManager;

    /**
     * @param WorkflowStateManagerInterface $workflowStateManager
     */
    public function __construct(WorkflowStateManagerInterface $workflowStateManager)
    {
        $this->workflowStateManager = $workflowStateManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderState(OrderInterface $order)
    {
        $orderState = $this->workflowStateManager->getStateInfo('coreshop_order', $order->getOrderState(), true);
        $paymentState = $this->workflowStateManager->getStateInfo('coreshop_order_payment', $order->getPaymentState(), true);
        $shippingState = $this->workflowStateManager->getStateInfo('coreshop_order_shipment', $order->getShippingState(), true);
        $invoiceState = $this->workflowStateManager->getStateInfo('coreshop_order_invoice', $order->getInvoiceState(), true);

        // the calculated state tries to get the recent state for a customer.
        // if order is new, check if payment is in a waiting position.
        // if payment is done, check if shipping is in a waiting condition. and so on.
        $calculatedState = $orderState['state'];

        // order has been canceled or is done.
        if (OrderStates::STATE_NEW !== $calculatedState) {
            $calculatedState = $orderState['label'];
        } else {
            if (OrderPaymentStates::STATE_PAID !== $paymentState['state']) {
                $calculatedState = $paymentState['label'];
            } elseif (OrderShipmentStates::STATE_SHIPPED !== $shippingState['state']) {
                $calculatedState = $shippingState['label'];
            }
        }

        return [
            'orderState' => $orderState['label'],
            'paymentState' => $paymentState['label'],
            'shippingState' => $shippingState['label'],
            'invoiceState' => $invoiceState['label'],
            'calculatedState' => $calculatedState,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_order_state';
    }
}
