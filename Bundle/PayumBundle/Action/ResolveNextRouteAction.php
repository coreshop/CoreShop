<?php

namespace CoreShop\Bundle\PayumBundle\Action;

use CoreShop\Bundle\PayumBundle\Request\ResolveNextRoute;
use CoreShop\Component\Payment\Model\PaymentInterface;
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

        if ($payment->getState() === "complete") {
            $request->setRouteName(
                'coreshop_shop_order_thank_you'
            );

            return;
        }

        /**
         * We could return the Customer to the last checkout page as well?
         */
        $request->setRouteName('coreshop_shop_checkout_error');
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof ResolveNextRoute &&
            $request->getFirstModel() instanceof PaymentInterface
        ;
    }
}
