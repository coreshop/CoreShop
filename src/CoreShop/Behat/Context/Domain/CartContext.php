<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
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
     * @param CartContextInterface $cartContext
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CartContextInterface $cartContext
    )
    {
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
        Assert::eq(
            $shipping,
            $this->cartContext->getCart()->getShipping(false),
            sprintf(
                'Cart shipping is expected to be %s, but it is %s',
                $shipping,
                $this->cartContext->getCart()->getShipping(false)
            )
        );
    }

    /**
     * @Then /^the cart shipping should be "([^"]+)" including tax$/
     */
    public function cartShippingCostShouldBeIncludingTax($shipping)
    {
        Assert::eq(
            $shipping,
            $this->cartContext->getCart()->getShipping(true),
            sprintf(
                'Cart shipping is expected to be %s, but it is %s',
                $shipping,
                $this->cartContext->getCart()->getShipping(true)
            )
        );
    }

    /**
     * @Then /^the cart should use (carrier "[^"]+")$/
     */
    public function cartShouldUseCarrier(CarrierInterface $carrier)
    {
        Assert::eq(
            $carrier->getId(),
            $this->cartContext->getCart()->getCarrier()->getId(),
            sprintf(
                'Cart is expected to use carrier %s, but found %s',
                $carrier->getIdentifier(),
                $this->cartContext->getCart()->getCarrier()->getName()
            )
        );
    }

    /**
     * @Then /^the cart should not have a carrier$/
     */
    public function cartShouldNotHaveACarrier()
    {
        Assert::null(
            $this->cartContext->getCart()->getCarrier(),
            'Cart is expected to not have a carrier but found one'
        );
    }
}
