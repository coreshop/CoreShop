<?php

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class CurrencyController extends FrontendController
{
    public function widgetAction(Request $request)
    {
        $this->get('coreshop.repository.country')->findForStore($this->get('coreshop.context.shopper')->getStore());

        return $this->render('CoreShopFrontendBundle:Currency:_widget.html.twig', [
            'currencies' => $this->getCurrencyRepository()->findActive()
        ]);
    }

    /**
     * @return CurrencyRepositoryInterface
     */
    protected function getCurrencyRepository() {
        /**
         * @var $repo CurrencyRepositoryInterface
         */
        $repo = $this->get('coreshop.repository.currency');

        return $repo;
    }
}
