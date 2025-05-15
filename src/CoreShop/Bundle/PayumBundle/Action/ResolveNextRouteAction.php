<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\PayumBundle\Action;

use CoreShop\Bundle\PayumBundle\Request\ResolveNextRoute;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\PaymentInterface;
use Payum\Core\Action\ActionInterface;

final class ResolveNextRouteAction implements ActionInterface
{
    /**
     * @inheritdoc
     *
     * @param ResolveNextRoute $request
     */
    public function execute($request): void
    {
        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();
        $order = $payment->getOrder();

        if ($order instanceof OrderInterface) {
            $request->setRouteParameters([
                '_locale' => $order->getLocaleCode(),
            ]);

            if ($payment->getState() === PaymentInterface::STATE_COMPLETED ||
                $payment->getState() === PaymentInterface::STATE_AUTHORIZED ||
                $payment->getState() === PaymentInterface::STATE_PROCESSING
            ) {
                $request->setRouteName('coreshop_checkout_thank_you');
                $request->setRouteParameters([
                    '_locale' => $order->getLocaleCode(),
                    'token' => $order->getToken(),
                ]);

                return;
            }

            $request->setRouteName('coreshop_order_revise');
            $request->setRouteParameters(array_merge($request->getRouteParameters(), ['token' => $order->getToken(), 'paymentId' => $payment->getId()]));
        }
    }

    public function supports($request): bool
    {
        return
            $request instanceof ResolveNextRoute &&
            $request->getFirstModel() instanceof PaymentInterface;
    }
}
