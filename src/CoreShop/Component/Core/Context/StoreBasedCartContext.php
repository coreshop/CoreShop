<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Context;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Locale\Context\LocaleNotFoundException;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Context\CartNotFoundException;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;

final class StoreBasedCartContext implements CartContextInterface
{
    private ?OrderInterface $cart = null;

    public function __construct(
        private CartContextInterface $cartContext,
        private ShopperContextInterface $shopperContext,
    ) {
    }

    public function getStorageList(): OrderInterface
    {
        return $this->getCart();
    }

    public function getCart(): OrderInterface
    {
        if (null !== $this->cart) {
            return $this->cart;
        }

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
            /**
             * @var CustomerInterface $customer
             */
            $customer = $this->shopperContext->getCustomer();
            $this->setCustomerAndAddressOnCart($cart, $customer);
        }

        $this->cart = $cart;

        return $cart;
    }

    private function setCustomerAndAddressOnCart(OrderInterface $cart, CustomerInterface $customer): void
    {
        $cart->setCustomer($customer);

        $defaultAddress = $customer->getDefaultAddress();
        if (null !== $defaultAddress) {
            $cart->setShippingAddress($defaultAddress);
            $cart->setInvoiceAddress($defaultAddress);
        }
    }
}
