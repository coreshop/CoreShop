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

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Bundle\CoreBundle\Form\Type\Checkout\CarrierType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Checkout\OptionalCheckoutStepInterface;
use CoreShop\Component\Order\Checkout\ValidationCheckoutStepInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Shipping\Resolver\CarriersResolverInterface;
use CoreShop\Component\Shipping\Validator\ShippableCarrierValidatorInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ShippingCheckoutStep implements CheckoutStepInterface, OptionalCheckoutStepInterface, ValidationCheckoutStepInterface
{
    /**
     * @var CarriersResolverInterface
     */
    private $carriersResolver;

    /**
     * @var ShippableCarrierValidatorInterface
     */
    private $shippableCarrierValidator;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @var CartManagerInterface
     */
    private $cartManager;

    /**
     * @param CarriersResolverInterface          $carriersResolver
     * @param ShippableCarrierValidatorInterface $shippableCarrierValidator
     * @param FormFactoryInterface               $formFactory
     * @param StoreContextInterface              $storeContext
     * @param CartManagerInterface               $cartManager
     */
    public function __construct(
        CarriersResolverInterface $carriersResolver,
        ShippableCarrierValidatorInterface $shippableCarrierValidator,
        FormFactoryInterface $formFactory,
        StoreContextInterface $storeContext,
        CartManagerInterface $cartManager
    ) {
        $this->carriersResolver = $carriersResolver;
        $this->shippableCarrierValidator = $shippableCarrierValidator;
        $this->formFactory = $formFactory;
        $this->storeContext = $storeContext;
        $this->cartManager = $cartManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'shipping';
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired(CartInterface $cart)
    {
        return $cart->hasShippableItems();
    }

    /**
     * {@inheritdoc}
     */
    public function doAutoForward(CartInterface $cart)
    {
        return false === $cart->hasShippableItems();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        return false === $cart->hasShippableItems()
            || ($cart->hasItems() &&
                $cart->getCarrier() instanceof CarrierInterface &&
                $cart->getShippingAddress() instanceof AddressInterface &&
                $this->shippableCarrierValidator->isCarrierValid($cart->getCarrier(), $cart, $cart->getShippingAddress()));
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CartInterface $cart, Request $request)
    {
        $form = $this->createForm($request, $this->getCarriers($cart), $cart);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $cart = $form->getData();

                $this->cartManager->persistCart($cart);

                return true;
            } else {
                throw new CheckoutException('Shipping Form is invalid', 'coreshop.ui.error.coreshop_checkout_shipping_form_invalid');
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CartInterface $cart, Request $request)
    {
        //Get Carriers
        $carriers = $this->getCarriers($cart);

        return [
            'carriers' => $carriers,
            'form' => $this->createForm($request, $carriers, $cart)->createView(),
        ];
    }

    /**
     * @param CartInterface $cart
     *
     * @return array
     */
    private function getCarriers(CartInterface $cart)
    {
        $carriers = $this->carriersResolver->resolveCarriers($cart, $cart->getShippingAddress());

        return $carriers;
    }

    /**
     * @param Request $request
     * @param $carriers
     * @param CartInterface $cart
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createForm(Request $request, $carriers, CartInterface $cart)
    {
        $form = $this->formFactory->createNamed('', CarrierType::class, $cart, [
            'carriers' => $carriers,
            'cart' => $cart,
        ]);

        if ($request->isMethod('post')) {
            $form = $form->handleRequest($request);
        }

        return $form;
    }
}
