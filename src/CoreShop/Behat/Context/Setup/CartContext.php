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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Bundle\OrderBundle\Factory\AddToCartFactoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Bundle\OrderBundle\DTO\AddToCartInterface;
use CoreShop\Bundle\OrderBundle\Form\Type\AddToCartType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Factory\CartItemFactoryInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\StorageList\StorageListModifierInterface;
use CoreShop\Component\Store\Model\StoreInterface;
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
     * @var AddToCartFactoryInterface
     */
    private $addToCartFactory;

    /**
     * @var CartItemFactoryInterface
     */
    private $factory;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param SharedStorageInterface       $sharedStorage
     * @param CartContextInterface         $cartContext
     * @param StorageListModifierInterface $cartModifier
     * @param CartManagerInterface         $cartManager
     * @param AddToCartFactoryInterface    $addToCartFactory
     * @param CartItemFactoryInterface     $factory
     * @param FormFactoryInterface         $formFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CartContextInterface $cartContext,
        StorageListModifierInterface $cartModifier,
        CartManagerInterface $cartManager,
        AddToCartFactoryInterface $addToCartFactory,
        CartItemFactoryInterface $factory,
        FormFactoryInterface $formFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->cartContext = $cartContext;
        $this->cartModifier = $cartModifier;
        $this->cartManager = $cartManager;
        $this->addToCartFactory = $addToCartFactory;
        $this->factory = $factory;
        $this->formFactory = $formFactory;
    }

    /**
     * @Given /^I add the (product "[^"]+") to my cart$/
     * @Given /^I add another (product "[^"]+") to my cart$/
     */
    public function addProductToCart(ProductInterface $product)
    {
        $cart = $this->cartContext->getCart();

        $cartItem = $this->factory->createWithPurchasable($product);

        $this->cartModifier->addToList($cart, $cartItem);

        $this->cartManager->persistCart($cart);
    }

    /**
     * @Given /^I add the (product "[^"]+") to my cart from add-to-cart-form/
     * @Given /^I add another (product "[^"]+") to my cart from add-to-cart-form/
     */
    public function addProductToCartFromAddToCartFormForm(ProductInterface $product)
    {
        $cart = $this->cartContext->getCart();
        $cartItem = $this->factory->createWithPurchasable($product);
        $addToCart = $this->createAddToCart($cart, $cartItem);
        $form = $this->formFactory->create(AddToCartType::class, $addToCart, ['csrf_protection' => false]);

        $formData = [
            'cartItem' => [
                'quantity' => 1
            ]
        ];

        $form->submit($formData);

        $this->sharedStorage->set('add_to_cart_form', $form);
        $this->cartManager->persistCart($cart);
    }

    /**
     * @Given /^I add the (product "[^"]+" with unit "[^"]+") to my cart$/
     * @Given /^I add another (product "[^"]+" with unit "[^"]+") to my cart$/
     */
    public function addProductInUnitToCart(array $productAndUnit)
    {
        $cart = $this->cartContext->getCart();

        /**
         * @var CartItemInterface $cartItem
         */
        $cartItem = $this->factory->createWithPurchasable($productAndUnit['product']);
        $cartItem->setUnitDefinition($productAndUnit['unit']);

        $this->cartModifier->addToList($cart, $cartItem);

        $this->cartManager->persistCart($cart);
    }

    /**
     * @Given /^I remove the (product "[^"]+") from my cart$/
     * @Given /^I remove another (product "[^"]+") from my cart$/
     */
    public function removeProductFromCart(ProductInterface $product)
    {
        $cart = $this->cartContext->getCart();

        foreach ($cart->getItems() as $cartItem) {
            if ($cartItem->getProduct()->getId() === $product->getId()) {
                $this->cartModifier->removeFromList($cart, $cartItem);

                $this->cartManager->persistCart($cart);
            }
        }
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

    /**
     * @Given /^(my cart) uses (currency "[^"]+")$/
     */
    public function myCartIsUsingCurrency(CartInterface $cart, CurrencyInterface $currency)
    {
        $cart->setCurrency($currency);

        $this->cartManager->persistCart($cart);
    }

    /**
     * @Given /^(my cart) uses (store "[^"]+")$/
     */
    public function myCartIsUsingStore(CartInterface $cart, StoreInterface $store)
    {
        $cart->setStore($store);

        $this->cartManager->persistCart($cart);
    }

    /**
     * @Given /^I refresh (my cart)$/
     */
    public function iRefreshMyCart(CartInterface $cart)
    {
        $this->cartManager->persistCart($cart);
    }

    /**
     * @param CartInterface     $cart
     * @param CartItemInterface $cartItem
     *
     * @return AddToCartInterface
     */
    protected function createAddToCart(CartInterface $cart, CartItemInterface $cartItem)
    {
        return $this->addToCartFactory->createWithCartAndCartItem($cart, $cartItem);
    }
}
