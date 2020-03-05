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

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Bundle\CoreBundle\Form\Type\Checkout\CarrierType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Checkout\OptionalCheckoutStepInterface;
use CoreShop\Component\Order\Checkout\ValidationCheckoutStepInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Shipping\Resolver\CarriersResolverInterface;
use CoreShop\Component\Shipping\Validator\ShippableCarrierValidatorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

class ShippingCheckoutStep implements CheckoutStepInterface, OptionalCheckoutStepInterface, ValidationCheckoutStepInterface
{
    private $carriersResolver;
    private $shippableCarrierValidator;
    private $formFactory;
    private $cartManager;

    public function __construct(
        CarriersResolverInterface $carriersResolver,
        ShippableCarrierValidatorInterface $shippableCarrierValidator,
        FormFactoryInterface $formFactory,
        CartManagerInterface $cartManager
    ) {
        $this->carriersResolver = $carriersResolver;
        $this->shippableCarrierValidator = $shippableCarrierValidator;
        $this->formFactory = $formFactory;
        $this->cartManager = $cartManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): string
    {
        return 'shipping';
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired(OrderInterface $cart): bool
    {
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\OrderInterface::class);

        return $cart->hasShippableItems();
    }

    /**
     * {@inheritdoc}
     */
    public function doAutoForward(OrderInterface $cart): bool
    {
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\OrderInterface::class);

        return $cart->hasShippableItems() === false;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(OrderInterface $cart): bool
    {
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\OrderInterface::class);

        return $cart->hasShippableItems() === false
            || ($cart->hasItems() &&
                $cart->getCarrier() instanceof CarrierInterface &&
                $cart->getShippingAddress() instanceof AddressInterface &&
                $this->shippableCarrierValidator->isCarrierValid($cart->getCarrier(), $cart, $cart->getShippingAddress()));
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(OrderInterface $cart, Request $request): bool
    {
        $form = $this->createForm($request, $this->getCarriers($cart), $cart);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $cart = $form->getData();

                $this->cartManager->persistCart($cart);

                return true;
            }

            throw new CheckoutException('Shipping Form is invalid', 'coreshop.ui.error.coreshop_checkout_shipping_form_invalid');
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(OrderInterface $cart, Request $request): array
    {
        //Get Carriers
        $carriers = $this->getCarriers($cart);

        return [
            'carriers' => $carriers,
            'form' => $this->createForm($request, $carriers, $cart)->createView(),
        ];
    }

    /**
     * @param OrderInterface $cart
     *
     * @return \CoreShop\Component\Shipping\Model\CarrierInterface[]
     */
    private function getCarriers(OrderInterface $cart): array
    {
        /**
         * @var \CoreShop\Component\Core\Model\OrderInterface $cart
         */
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\OrderInterface::class);

        return $this->carriersResolver->resolveCarriers($cart, $cart->getShippingAddress());
    }

    private function createForm(Request $request, array$carriers, OrderInterface $cart): FormInterface
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
