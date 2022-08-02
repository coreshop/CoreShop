<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\PayumBundle\Action\Offline;

use CoreShop\Bundle\PayumBundle\Request\ConfirmOrder;
use CoreShop\Bundle\WorkflowBundle\Applier\StateMachineApplier;
use CoreShop\Component\Core\Model\PaymentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\OrderTransitions;
use Payum\Core\Action\ActionInterface;

final class ConfirmOrderAction implements ActionInterface
{
    public function __construct(private StateMachineApplier $stateMachineApplier)
    {
    }

    public function execute($request): void
    {
        $payment = $request->getFirstModel();
        $order = $payment->getOrder();

        if ($order instanceof OrderInterface) {
            //Confirm
            $this->stateMachineApplier->apply($order, OrderTransitions::IDENTIFIER, OrderTransitions::TRANSITION_CONFIRM);

            return;
        }

        //Shouldn't actually happen -> maybe cancel?
    }

    public function supports($request): bool
    {
        return
            $request instanceof ConfirmOrder &&
            $request->getFirstModel() instanceof PaymentInterface;
    }
}
