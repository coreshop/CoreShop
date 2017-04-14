<?php

namespace CoreShop\Bundle\PayumBundle\Action\Offline;

use Payum\Core\Action\ActionInterface;
use CoreShop\Bundle\PayumBundle\Request\ResolveNextRoute;
use CoreShop\Component\Payment\Model\PaymentInterface;

final class ResolveNextRouteAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     *
     * @param ResolveNextRoute $request
     */
    public function execute($request)
    {
        $request->setRouteName('coreshop_shop_order_thank_you');
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
