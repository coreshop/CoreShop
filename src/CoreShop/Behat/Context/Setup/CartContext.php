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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\StorageList\StorageListModifierInterface;
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
     * @var StorageListModifierInterface
     */
    private $cartModifier;

    /**
     * @var CartManagerInterface
     */
    private $cartManager;

    /**
     * @param SharedStorageInterface       $sharedStorage
     * @param CartContextInterface         $cartContext
     * @param StorageListModifierInterface $cartModifier
     * @param CartManagerInterface         $cartManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CartContextInterface $cartContext,
        StorageListModifierInterface $cartModifier,
        CartManagerInterface $cartManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->cartContext = $cartContext;
        $this->cartModifier = $cartModifier;
        $this->cartManager = $cartManager;
    }

    /**
     * @Given /^I add the (product "[^"]+") to my cart$/
     * @Given /^I add another (product "[^"]+") to my cart$/
     */
    public function addProductToCart(ProductInterface $product)
    {
        $cart = $this->cartContext->getCart();

        $this->cartModifier->addItem($cart, $product);

        $this->cartManager->persistCart($cart);
    }

    /**
     * @Given /^the cart belongs to (customer "[^"]+")$/
     */
    public function theCartBelongsToCustomer(CustomerInterface $customer)
    {
        $this->cartContext->getCart()->setCustomer($customer);

        $this->cartManager->persistCart($this->cartContext->getCart());
    }

    /**
     * @Given /^the cart ships to (customer "[^"]+") first address$/
     */
    public function theCartShipsToCustomersFirstAddress(CustomerInterface $customer)
    {
        Assert::greaterThan(count($customer->getAddresses()), 0);

        $this->cartContext->getCart()->setShippingAddress(reset($customer->getAddresses()));
        $this->cartManager->persistCart($this->cartContext->getCart());
    }

    /**
     * @Given /^the cart ships to (customer "[^"]+") address with postcode "([^"]+)"$/
     */
    public function theCartShipsToCustomersAddressWithPostcode(CustomerInterface $customer, $postcode)
    {
        Assert::greaterThan(count($customer->getAddresses()), 0);

        $address = current(array_filter($customer->getAddresses(), function ($address) use ($postcode) {
            return $address->getPostcode() === $postcode;
        }));

        Assert::isInstanceOf($address, AddressInterface::class);

        $this->cartContext->getCart()->setShippingAddress($address);
        $this->cartManager->persistCart($this->cartContext->getCart());
    }

    /**
     * @Given /^the cart invoices to (customer "[^"]+") address with postcode "([^"]+)"$/
     */
    public function theCartInvoicesToCustomersAddressWithPostcode(CustomerInterface $customer, $postcode)
    {
        Assert::greaterThan(count($customer->getAddresses()), 0);

        $address = current(array_filter($customer->getAddresses(), function ($address) use ($postcode) {
            return $address->getPostcode() === $postcode;
        }));

        Assert::isInstanceOf($address, AddressInterface::class);

        $this->cartContext->getCart()->setInvoiceAddress($address);
        $this->cartManager->persistCart($this->cartContext->getCart());
    }
}
