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
use CoreShop\Bundle\WorkflowBundle\Applier\StateMachineApplier;
use CoreShop\Component\Core\Model\PaymentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\OrderTransitions;
use Payum\Core\Action\ActionInterface;

final class ConfirmOrderAction implements ActionInterface
{
    /**
     * @var StateMachineApplier
     */
    private $stateMachineApplier;

    /**
     * @param StateMachineApplier $stateMachineApplier
     */
    public function __construct(StateMachineApplier $stateMachineApplier)
    {
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
        $order = $payment->getOrder();

        \Pimcore\Logger::log('ConfirmOrderAction: ' . $payment->getState());

        if ($order instanceof OrderInterface) {
            if ($payment->getState() === PaymentInterface::STATE_COMPLETED ||
                $payment->getState() === PaymentInterface::STATE_AUTHORIZED
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
