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

use CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfiguratorInterface;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Currency\CurrencyStorageInterface;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CurrencyController extends FrontendController
{
    public function widgetAction(
        CurrencyRepositoryInterface $currencyRepository,
        ShopperContextInterface $shopperContext,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $currencies = $currencyRepository->findActiveForStore($shopperContext->getStore());

        return $this->renderTemplate($templateConfigurator->findTemplate('Currency/_widget.html'), [
            'currencies' => $currencies,
        ]);
    }

    public function switchAction(
        Request $request,
        CurrencyRepositoryInterface $currencyRepository,
        CartManagerInterface $cartManager,
        ShopperContextInterface $shopperContext,
        CurrencyStorageInterface $currencyStorage
    ): Response {
        $currencyCode = $request->get('currencyCode');
        $currency = $currencyRepository->getByCode($currencyCode);

        if (null === $currency) {
            return new RedirectResponse($request->headers->get('referer', $request->getSchemeAndHttpHost()));
        }

        $cart = $shopperContext->getCart();

        $store = $shopperContext->getStore();
        $currencyStorage->set($store, $currency);

        $cart->setCurrency($currency);

        if ($cart->hasItems()) {
            $cartManager->persistCart($cart);
        }

        return new RedirectResponse($request->headers->get('referer', $request->getSchemeAndHttpHost()));
    }
}
