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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\CartItem;
use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Product\Model\ProductUnitInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Pimcore\Model\DataObject\Fieldcollection;
use Symfony\Component\Form\FormInterface;
use Webmozart\Assert\Assert;

final class CartContext implements Context
{
    private $sharedStorage;
    private $cartContext;

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
        $cart = $this->cartContext->getCart();
        $foundItem = null;

        foreach ($cart->getItems() as $cartItem) {
            if ($cartItem->getProduct()->getId() === $product->getId()) {
                $foundItem = $cartItem;

                break;
            }
        }

        Assert::null(
            $foundItem,
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
     * @Then /^the cart item taxes should be "([^"]+)"$/
     */
    public function cartItemTaxesShouldBe($totalTax)
    {
        $cart = $this->cartContext->getCart();
        $itemTaxesTotal = 0;

        foreach ($cart->getItems() as $item) {
            $taxesFc = $item->getTaxes();

            if (!$taxesFc instanceof Fieldcollection) {
                continue;
            }
            /**
             * @var TaxItemInterface $tax
             */
            foreach ($taxesFc->getItems() as $tax) {
                $itemTaxesTotal += $tax->getAmount();
            }
        }

        Assert::eq(
            $totalTax,
            $itemTaxesTotal,
            sprintf(
                'Cart item taxes is expected to be %s, but it is %s',
                $totalTax,
                $itemTaxesTotal
            )
        );
    }

    /**
     * @Then /^the cart should weigh ([^"]+)kg$/
     */
    public function cartShouldWeigh($kg)
    {
        $cart = $this->cartContext->getCart();

        /**
         * @var CartInterface $cart
         */
        Assert::isInstanceOf($cart, CartInterface::class);

        Assert::eq(
            $kg,
            $cart->getWeight(),
            sprintf(
                'Cart is expected to weigh %skg, but it weighs %skg',
                $kg,
                $cart->getWeight()
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

    /**
     * @Then /^the first item in (my cart) should have (unit "([^"]+)")$/
     */
    public function theFirstItemInMyCartShouldHaveUnit(CartInterface $cart, ProductUnitInterface $unit)
    {
        Assert::minCount(
            $cart->getItems(),
            1,
            'Expected to be at least 1 item in the cart, but found none'
        );

        /**
         * @var CartItem $item
         */
        $item = $cart->getItems()[0];

        Assert::notNull(
            $item->getUnitDefinition(),
            'Expected first cart item to have a unit-definition, but it did not'
        );

        Assert::eq(
            $item->getUnitDefinition()->getUnit(),
            $unit,
            sprintf(
                'Expected cart item to have unit %s, but found %s',
                $item->getUnitDefinition()->getUnitName(),
                $unit->getName()
            )
        );
    }

    /**
     * @Then /^the second item in (my cart) should have (unit "([^"]+)")$/
     */
    public function theSecondItemInMyCartShouldHaveUnit(CartInterface $cart, ProductUnitInterface $unit)
    {
        Assert::minCount(
            $cart->getItems(),
            2,
            sprintf('Expected to be at least 2 items in the cart, but found %s', count($cart->getItems()))
        );

        /**
         * @var CartItem $item
         */
        $item = $cart->getItems()[1];

        Assert::notNull(
            $item->getUnitDefinition(),
            'Expected first cart item to have a unit-definition, but it did not'
        );

        Assert::eq(
            $item->getUnitDefinition()->getUnit(),
            $unit,
            sprintf(
                'Expected cart item to have unit %s, but found %s',
                $item->getUnitDefinition()->getUnitName(),
                $unit->getName()
            )
        );
    }

    /**
     * @Then /^there should be a violation message in my (add-to-cart-form) with message "([^"]+)"$/
     */
    public function thereShouldBeCartFormViolation(FormInterface $addToCartForm, $message)
    {
        Assert::greaterThan($addToCartForm->getErrors()->count(), 0);

        foreach ($addToCartForm->getErrors(true, true) as $error) {
            Assert::eq(
                $error->getMessage(),
                $message
            );
        }
    }
}
