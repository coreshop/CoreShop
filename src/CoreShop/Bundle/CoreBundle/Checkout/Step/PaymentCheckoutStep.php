<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Bundle\CoreBundle\Form\Type\Checkout\PaymentType;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Checkout\OptionalCheckoutStepInterface;
use CoreShop\Component\Order\Checkout\ValidationCheckoutStepInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class PaymentCheckoutStep implements CheckoutStepInterface, OptionalCheckoutStepInterface, ValidationCheckoutStepInterface
{
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
     * @param FormFactoryInterface  $formFactory
     * @param StoreContextInterface $storeContext
     * @param CartManagerInterface  $cartManager
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        StoreContextInterface $storeContext,
        CartManagerInterface $cartManager
    ) {
        $this->formFactory = $formFactory;
        $this->storeContext = $storeContext;
        $this->cartManager = $cartManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'payment';
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired(CartInterface $cart)
    {
        return $cart->getTotal() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function doAutoForward(CartInterface $cart)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        return $cart->hasItems() && $cart->getPaymentProvider() instanceof PaymentProviderInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CartInterface $cart, Request $request)
    {
        $form = $this->createForm($request, $cart);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $cart = $form->getData();

                $this->cartManager->persistCart($cart);

                return true;
            } else {
                throw new CheckoutException('Payment Form is invalid', 'coreshop.ui.error.coreshop_checkout_payment_form_invalid');
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CartInterface $cart, Request $request)
    {
        return [
            'form' => $this->createForm($request, $cart)->createView(),
        ];
    }

    /**
     * @param Request       $request
     * @param CartInterface $cart
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createForm(Request $request, CartInterface $cart)
    {
        $form = $this->formFactory->createNamed('coreshop', PaymentType::class, $cart, [
            'payment_subject' => $cart,
        ]);

        if ($request->isMethod('post')) {
            $form = $form->handleRequest($request);
        }

        return $form;
    }
}
