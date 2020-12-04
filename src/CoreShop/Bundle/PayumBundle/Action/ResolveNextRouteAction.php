<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\PayumBundle\Action;

use CoreShop\Bundle\PayumBundle\Request\ResolveNextRoute;
use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Core\Model\PaymentInterface;
use Payum\Core\Action\ActionInterface;

final class ResolveNextRouteAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     *
     * @param ResolveNextRoute $request
     */
    public function execute($request)
    {
        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();
        $order = $payment->getOrder();

        if ($order instanceof PayableInterface) {
            $request->setRouteParameters([
                '_locale' => $order->getLocaleCode(),
            ]);

            if ($payment->getState() === PaymentInterface::STATE_COMPLETED ||
                $payment->getState() === PaymentInterface::STATE_AUTHORIZED
            ) {
                $request->setRouteName('coreshop_checkout_confirmation');

                return;
            }

            $request->setRouteName('coreshop_order_revise');
            $request->setRouteParameters(array_merge($request->getRouteParameters(), ['token' => $order->getToken(), 'paymentId' => $payment->getId()]));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof ResolveNextRoute &&
            $request->getFirstModel() instanceof PaymentInterface;
    }
}
