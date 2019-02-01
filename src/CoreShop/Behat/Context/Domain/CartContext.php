<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use Webmozart\Assert\Assert;

final class CartContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param CartContextInterface   $cartContext
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CartContextInterface $cartContext
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->cartContext = $cartContext;
    }

    /**
     * @Then /^there should be one product in my cart$/
     */
    public function thereShouldBeOneProductInTheCart()
    {
        Assert::eq(
            count($this->cartContext->getCart()->getItems()),
            1,
            sprintf(
                'There should be only one product in the cart, but found %d',
                count($this->cartContext->getCart()->getItems())
            )
        );
    }

    /**
     * @Then /^there should be two products in my cart$/
     */
    public function thereShouldBeTwoProductsInTheCart()
    {
        Assert::eq(
            count($this->cartContext->getCart()->getItems()),
            2,
            sprintf(
                'There should be only two products in the cart, but found %d',
                count($this->cartContext->getCart()->getItems())
            )
        );
    }

    /**
     * @Then /^the (product "[^"]+") should not be in my cart$/
     */
    public function theProductShouldNotBeInMyCart(ProductInterface $product)
    {
        Assert::null(
            $this->cartContext->getCart()->getItemForProduct($product),
            sprintf(
                'Product %s found in cart',
                $product->getName()
            )
        );
    }

    /**
     * @Then /^the (product "[^"]+") should be in my cart$/
     */
    public function theProductShouldBeInMyCart(ProductInterface $product)
    {
        /**
         * @var CartItemInterface $cartItem
         */
        $cartItem = null;

        foreach ($this->cartContext->getCart()->getItems() as $item) {
            if ($item->getIsGiftItem()) {
                continue;
            }

            if ($item->getProduct()->getId() === $product->getId()) {
                $cartItem = $item;

                break;
            }
        }

        Assert::notNull(
            $cartItem,
            sprintf(
                'Product %s not found in cart',
                $product->getName()
            )
        );
    }

    /**
     * @Then /^the (product "[^"]+") should be in my cart as gift$/
     */
    public function theProductShouldBeInMyCartAsGift(ProductInterface $product)
    {
        /**
         * @var CartItemInterface $cartItem
         */
        $cartItem = null;

        foreach ($this->cartContext->getCart()->getItems() as $item) {
            if (!$item->getIsGiftItem()) {
                continue;
            }

            if ($item->getProduct()->getId() === $product->getId()) {
                $cartItem = $item;

                break;
            }
        }

        Assert::true(
            $cartItem ? $cartItem->getIsGiftItem() : false,
            sprintf(
                'Product %s is not in the Cart or is not a gift',
                $product->getName()
            )
        );
    }

    /**
     * @Then /^the cart total should be "([^"]+)" including tax$/
     */
    public function cartTotalShouldBeIncludingTax($total)
    {
        Assert::eq(
            $total,
            $this->cartContext->getCart()->getTotal(true),
            sprintf(
                'Cart total is expected to be %s, but it is %s',
                $total,
                $this->cartContext->getCart()->getTotal(true)
            )
        );
    }

    /**
     * @Then /^the cart total should be "([^"]+)" excluding tax$/
     */
    public function cartTotalShouldBeExcludingTax($total)
    {
        Assert::eq(
            $total,
            $this->cartContext->getCart()->getTotal(false),
            sprintf(
                'Cart total is expected to be %s, but it is %s',
                $total,
                $this->cartContext->getCart()->getTotal(false)
            )
        );
    }

    /**
     * @Then /^the cart subtotal should be "([^"]+)" including tax$/
     */
    public function cartSubtotalShouldBeIncludingTax($total)
    {
        Assert::eq(
            $total,
            $this->cartContext->getCart()->getSubtotal(true),
            sprintf(
                'Cart subtotal is expected to be %s, but it is %s',
                $total,
                $this->cartContext->getCart()->getSubtotal(true)
            )
        );
    }

    /**
     * @Then /^the cart subtotal should be "([^"]+)" excluding tax$/
     */
    public function cartSubtotalShouldBeExcludingTax($total)
    {
        Assert::eq(
            $total,
            $this->cartContext->getCart()->getSubtotal(false),
            sprintf(
                'Cart subtotal is expected to be %s, but it is %s',
                $total,
                $this->cartContext->getCart()->getSubtotal(false)
            )
        );
    }

    /**
     * @Then /^the cart total tax should be "([^"]+)"$/
     */
    public function cartTotalTaxShouldBe($totalTax)
    {
        Assert::eq(
            $totalTax,
            $this->cartContext->getCart()->getTotalTax(),
            sprintf(
                'Cart total is expected to be %s, but it is %s',
                $totalTax,
                $this->cartContext->getCart()->getTotalTax()
            )
        );
    }

    /**
     * @Then /^the cart should weigh ([^"]+)kg$/
     */
    public function cartShouldWeigh($kg)
    {
        Assert::eq(
            $kg,
            $this->cartContext->getCart()->getWeight(),
            sprintf(
                'Cart is expected to weigh %skg, but it weighs %skg',
                $kg,
                $this->cartContext->getCart()->getWeight()
            )
        );
    }

    /**
     * @Then /^the cart shipping should be "([^"]+)" excluding tax$/
     */
    public function cartShippingCostShouldBeExcludingTax($shipping)
    {
        $cart = $this->cartContext->getCart();

        Assert::isInstanceOf($cart, CartInterface::class);

        Assert::eq(
            $shipping,
            $cart->getShipping(false),
            sprintf(
                'Cart shipping is expected to be %s, but it is %s',
                $shipping,
                $cart->getShipping(false)
            )
        );
    }

    /**
     * @Then /^the cart shipping should be "([^"]+)" including tax$/
     */
    public function cartShippingCostShouldBeIncludingTax($shipping)
    {
        $cart = $this->cartContext->getCart();

        Assert::isInstanceOf($cart, CartInterface::class);

        Assert::eq(
            $shipping,
            $cart->getShipping(true),
            sprintf(
                'Cart shipping is expected to be %s, but it is %s',
                $shipping,
                $cart->getShipping(true)
            )
        );
    }

    /**
     * @Then /^the (carts) shipping tax rate should be "([^"]+)"$/
     * @Then /^the (loaded carts) shipping tax rate should be "([^"]+)"$/
     */
    public function cartShippingTaxRateShouldBe(CartInterface $cart, $shippingTaxRate)
    {
        Assert::eq(
            $shippingTaxRate,
            $cart->getShippingTaxRate(),
            sprintf(
                'Cart shipping is expected to be %s, but it is %s',
                $shippingTaxRate,
                $cart->getShippingTaxRate()
            )
        );
    }

    /**
     * @Then /^the cart should use (carrier "[^"]+")$/
     */
    public function cartShouldUseCarrier(CarrierInterface $carrier)
    {
        $cart = $this->cartContext->getCart();

        Assert::isInstanceOf($cart, CartInterface::class);

        Assert::eq(
            $carrier->getId(),
            $cart->getCarrier()->getId(),
            sprintf(
                'Cart is expected to use carrier %s, but found %s',
                $carrier->getTitle('en'),
                $cart->getCarrier()->getTitle('en')
            )
        );
    }

    /**
     * @Then /^the cart should not have a carrier$/
     */
    public function cartShouldNotHaveACarrier()
    {
        $cart = $this->cartContext->getCart();

        Assert::isInstanceOf($cart, CartInterface::class);

        Assert::null(
            $cart->getCarrier(),
            'Cart is expected to not have a carrier but found one'
        );
    }

    /**
     * @Then /^the cart discount should be "([^"]+)" including tax$/
     */
    public function cartDiscountShouldBeIncludingTax($total)
    {
        Assert::eq(
            $total,
            $this->cartContext->getCart()->getDiscount(true),
            sprintf(
                'Cart discount is expected to be %s, but it is %s',
                $total,
                $this->cartContext->getCart()->getDiscount(true)
            )
        );
    }

    /**
     * @Then /^the cart discount should be "([^"]+)" excluding tax$/
     */
    public function cartDiscountShouldBeExcludingTax($total)
    {
        Assert::eq(
            $total,
            $this->cartContext->getCart()->getDiscount(false),
            sprintf(
                'Cart discount is expected to be %s, but it is %s',
                $total,
                $this->cartContext->getCart()->getDiscount(false)
            )
        );
    }

    /**
     * @Then /^there should be no product in (my cart)$/
     */
    public function thereShouldBeNoProductInMyCart(CartInterface $cart)
    {
        Assert::eq(
            count($cart->getItems()),
            0,
            sprintf(
                'There should be no product in the cart, but found %d',
                count($cart->getItems())
            )
        );
    }
}
