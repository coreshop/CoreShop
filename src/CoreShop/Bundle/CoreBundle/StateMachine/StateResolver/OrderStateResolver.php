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

namespace CoreShop\Bundle\CoreBundle\StateMachine\StateResolver;

use CoreShop\Bundle\CoreBundle\StateMachine\OrderPaymentStates;
use CoreShop\Bundle\CoreBundle\StateMachine\OrderShippingStates;
use CoreShop\Bundle\CoreBundle\StateMachine\OrderTransitions;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\StateMachine\StateMachineResolverInterface;
use Symfony\Component\Workflow\Registry;

final class OrderStateResolver implements StateMachineResolverInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(OrderInterface $order)
    {
        $stateMachine = $this->registry->get($order, 'coreshop_order');
        if ($this->canOrderBeFulfilled($order) && $stateMachine->can($order, OrderTransitions::TRANSITION_COMPLETE)) {
            $stateMachine->apply($order,OrderTransitions::TRANSITION_COMPLETE);
        }
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    private function canOrderBeFulfilled(OrderInterface $order)
    {
        return
            OrderPaymentStates::STATE_PAID === $order->getPaymentState() &&
            OrderShippingStates::STATE_SHIPPED === $order->getShippingState();
    }
}