<?php

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CurrencyController extends FrontendController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function widgetAction(Request $request)
    {
        $currencies = $this->get('coreshop.repository.currency')->findActiveForStore($this->get('coreshop.context.shopper')->getStore());

        return $this->render('CoreShopFrontendBundle:Currency:_widget.html.twig', [
            'currencies' => $currencies
        ]);
    }

    /**
     * @param Request $request
     * @param $currencyCode
     * @return RedirectResponse
     */
    public function switchAction(Request $request, $currencyCode) {

        $store = $this->get('coreshop.context.store')->getStore();
        $this->get('coreshop.storage.currency')->set($store, $currencyCode);

        return new RedirectResponse($request->headers->get('referer', $request->getSchemeAndHttpHost()));
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
