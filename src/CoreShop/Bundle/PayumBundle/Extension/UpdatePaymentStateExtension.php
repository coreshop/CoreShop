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

namespace CoreShop\Bundle\PayumBundle\Extension;

use CoreShop\Bundle\PayumBundle\Request\GetStatus;
use CoreShop\Component\Core\Model\PaymentInterface;
use CoreShop\Component\Payment\PaymentTransitions;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\Request\Generic;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Request\Notify;

final class UpdatePaymentStateExtension implements ExtensionInterface
{
    private StateMachineManager $stateMachineManager;

    public function __construct(StateMachineManager $stateMachineManager)
    {
        $this->stateMachineManager = $stateMachineManager;
    }

    public function onPreExecute(Context $context): void
    {
    }

    public function onExecute(Context $context): void
    {
    }

    public function onPostExecute(Context $context): void
    {
        if ($context->getException()) {
            return;
        }

        $previousStack = $context->getPrevious();
        $previousStackSize = count($previousStack);

        if ($previousStackSize > 1) {
            return;
        }

        if ($previousStackSize === 1) {
            $previousActionClassName = get_class($previousStack[0]->getAction());
            if (false === stripos($previousActionClassName, 'NotifyNullAction')) {
                return;
            }
        }

        /** @var Generic|bool $request */
        $request = $context->getRequest();
        if (false === $request instanceof Generic) {
            return;
        }

        if (false === $request instanceof GetStatusInterface && false === $request instanceof Notify) {
            return;
        }

        /** @var PaymentInterface|bool $payment */
        $payment = $request->getFirstModel();
        if (false === $payment instanceof PaymentInterface) {
            return;
        }

        $context->getGateway()->execute($status = new GetStatus($payment));
        $value = $status->getValue();
        if ($payment->getState() !== $value && PaymentInterface::STATE_UNKNOWN !== $value) {
            $this->updatePaymentState($payment, $value);
        }
    }

    private function updatePaymentState(PaymentInterface $payment, string $nextState): void
    {
        $workflow = $this->stateMachineManager->get($payment, PaymentTransitions::IDENTIFIER);
        if (null !== $transition = $this->stateMachineManager->getTransitionToState($workflow, $payment, $nextState)) {
            $workflow->apply($payment, $transition);
        }
    }
}
