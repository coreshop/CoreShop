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

namespace CoreShop\Component\Core\Context;

use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Locale\Context\LocaleNotFoundException;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Context\CartNotFoundException;
use CoreShop\Component\Store\Context\StoreNotFoundException;

final class StoreBasedCartContext implements CartContextInterface
{
    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @var ShopperContextInterface
     */
    private $shopperContext;

    /**
     * @var CartInterface|null
     */
    private $cart;

    /**
     * @param CartContextInterface    $cartContext
     * @param ShopperContextInterface $shopperContext
     */
    public function __construct(CartContextInterface $cartContext, ShopperContextInterface $shopperContext)
    {
        $this->cartContext = $cartContext;
        $this->shopperContext = $shopperContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        if (null !== $this->cart) {
            return $this->cart;
        }

        /** @var CartInterface $cart */
        $cart = $this->cartContext->getCart();

        try {
            /** @var StoreInterface $store */
            $store = $this->shopperContext->getStore();

            $cart->setStore($store);
            $cart->setCurrency($store->getCurrency());
            $cart->setLocaleCode($this->shopperContext->getLocaleCode());
        } catch (StoreNotFoundException $exception) {
            throw new CartNotFoundException('CoreShop was not able to prepare the cart.', $exception);
        } catch (CurrencyNotFoundException $exception) {
            throw new CartNotFoundException('CoreShop was not able to prepare the cart.', $exception);
        } catch (LocaleNotFoundException $exception) {
            throw new CartNotFoundException('CoreShop was not able to prepare the cart.', $exception);
        }

        if ($this->shopperContext->hasCustomer()) {
            $this->setCustomerAndAddressOnCart($cart, $this->shopperContext->getCustomer());
        }

        $this->cart = $cart;

        return $cart;
    }

    /**
     * @param CartInterface     $cart
     * @param CustomerInterface $customer
     */
    private function setCustomerAndAddressOnCart(CartInterface $cart, CustomerInterface $customer)
    {
        $cart->setCustomer($customer);

        $defaultAddress = $customer->getDefaultAddress();
        if (null !== $defaultAddress) {
            $cart->setShippingAddress($defaultAddress);
            $cart->setInvoiceAddress($defaultAddress);
        }
    }
}
