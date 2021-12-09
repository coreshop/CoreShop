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

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Currency\CurrencyStorageInterface;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CurrencyController extends FrontendController
{
    public function widgetAction(Request $request): Response
    {
        $currencies = $this->get('coreshop.repository.currency')->findActiveForStore($this->get(ShopperContextInterface::class)->getStore());

        return $this->render($this->templateConfigurator->findTemplate('Currency/_widget.html'), [
            'currencies' => $currencies,
        ]);
    }

    public function switchAction(Request $request): Response
    {
        $currencyCode = $this->getParameterFromRequest($request, 'currencyCode');
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

    protected function getCurrencyRepository(): CurrencyRepositoryInterface
    {
        return $this->get('coreshop.repository.currency');
    }
}
