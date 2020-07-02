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

declare(strict_types=1);

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Currency\CurrencyStorageInterface;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CurrencyController extends FrontendController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function widgetAction(Request $request)
    {
        $currencies = $this->get('coreshop.repository.currency')->findActiveForStore($this->get(ShopperContextInterface::class)->getStore());

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Currency/_widget.html'), [
            'currencies' => $currencies,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function switchAction(Request $request)
    {
        $currencyCode = $request->get('currencyCode');
        $currency = $this->getCurrencyRepository()->getByCode($currencyCode);
        $cartManager = $this->get(CartManagerInterface::class);
        $cartContext = $this->get(CartContextInterface::class);
        $cart = $cartContext->getCart();

        $store = $this->get(StoreContextInterface::class)->getStore();
        $this->get(CurrencyStorageInterface::class)->set($store, $currency);

        $cart->setCurrency($currency);

        if ($cart->hasItems()) {
            $cartManager->persistCart($cart);
        }

        return new RedirectResponse($request->headers->get('referer', $request->getSchemeAndHttpHost()));
    }

    /**
     * @return CurrencyRepositoryInterface
     */
    protected function getCurrencyRepository()
    {
        /**
         * @var CurrencyRepositoryInterface
         */
        $repo = $this->get('coreshop.repository.currency');

        return $repo;
    }
}
