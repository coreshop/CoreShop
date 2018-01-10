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

namespace CoreShop\Bundle\CoreBundle\Order\StateResolver;

use CoreShop\Component\Core\OrderPaymentStates;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\OrderShippingStates;
use CoreShop\Component\Order\OrderTransitions;
use CoreShop\Component\Order\StateResolver\StateResolverInterface;
use CoreShop\Component\Resource\Workflow\StateMachineManager;
use Webmozart\Assert\Assert;

final class OrderStateResolver implements StateResolverInterface
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
     * {@inheritdoc}
     */
    public function resolve(OrderInterface $order)
    {
        $stateMachine = $this->stateMachineManager->get($order, 'coreshop_order');
        if ($this->canOrderBeComplete($order) && $stateMachine->can($order, OrderTransitions::TRANSITION_COMPLETE)) {
            $stateMachine->apply($order, OrderTransitions::TRANSITION_COMPLETE);
        }
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    private function canOrderBeComplete(OrderInterface $order)
    {
        /**
         * @var $order \CoreShop\Component\Core\Model\OrderInterface
         */
        Assert::isInstanceOf($order, \CoreShop\Component\Core\Model\OrderInterface::class);

        return
            OrderPaymentStates::STATE_PAID === $order->getPaymentState() &&
            OrderShippingStates::STATE_SHIPPED === $order->getShippingState();
    }
}