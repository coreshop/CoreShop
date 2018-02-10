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

namespace CoreShop\Bundle\PayumBundle\Action;

use CoreShop\Bundle\PayumBundle\Request\ConfirmOrder;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\OrderTransitions;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Resource\Workflow\StateMachineApplier;
use Payum\Core\Action\ActionInterface;

final class ConfirmOrderAction implements ActionInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var StateMachineApplier
     */
    private $stateMachineApplier;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param StateMachineApplier $stateMachineApplier
     */
    public function __construct(OrderRepositoryInterface $orderRepository, StateMachineApplier $stateMachineApplier)
    {
        $this->orderRepository = $orderRepository;
        $this->stateMachineApplier = $stateMachineApplier;
    }

    /**
     * {@inheritdoc}
     *
     * @param ConfirmOrder $request
     */
    public function execute($request)
    {
        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();
        $order = $this->orderRepository->find($payment->getOrderId());

        if ($order instanceof OrderInterface) {
            $request->setRouteParameters([
                '_locale' => $order->getOrderLanguage()
            ]);

            if (
                $payment->getState() === PaymentInterface::STATE_COMPLETED ||
                $payment->getState() === PaymentInterface::STATE_PROCESSING
            ) {
                $this->stateMachineApplier->apply($order, OrderTransitions::IDENTIFIER, OrderTransitions::TRANSITION_CONFIRM);
                return;
            }

            //state stays new
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof ConfirmOrder &&
            $request->getFirstModel() instanceof PaymentInterface;
    }
}
