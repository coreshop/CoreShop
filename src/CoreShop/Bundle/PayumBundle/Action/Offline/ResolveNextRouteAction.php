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

namespace CoreShop\Bundle\PayumBundle\Action\Offline;

use CoreShop\Bundle\PayumBundle\Request\ResolveNextRoute;
use CoreShop\Component\Core\Model\PaymentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
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
        $payment = $request->getFirstModel();
        $order = $payment->getOrder();

        if ($order instanceof OrderInterface) {
            $request->setRouteName('coreshop_checkout_confirmation');
            $request->setRouteParameters([
                '_locale' => $order->getLocaleCode(),
            ]);

            return;
        }

        $request->setRouteName('coreshop_checkout_error');
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
