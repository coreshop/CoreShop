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
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Shipping\Discover\ShippableCarriersDiscoveryInterface;
use CoreShop\Component\Shipping\Validator\ShippableCarrierValidatorInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ShippingCheckoutStep implements CheckoutStepInterface
{
    /**
     * @var ShippableCarriersDiscoveryInterface
     */
    private $shippableCarriersDiscovery;

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
     * @param ShippableCarriersDiscoveryInterface $shippableCarriersDiscovery
     * @param ShippableCarrierValidatorInterface $shippableCarrierValidator
     * @param FormFactoryInterface $formFactory
     * @param StoreContextInterface $storeContext
     */
    public function __construct(
        ShippableCarriersDiscoveryInterface $shippableCarriersDiscovery,
        ShippableCarrierValidatorInterface $shippableCarrierValidator,
        FormFactoryInterface $formFactory,
        StoreContextInterface $storeContext
    )
    {
        $this->shippableCarriersDiscovery = $shippableCarriersDiscovery;
        $this->shippableCarrierValidator = $shippableCarrierValidator;
        $this->formFactory = $formFactory;
        $this->storeContext = $storeContext;
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
    public function doAutoForward(CartInterface $cart)
    {
        return $cart->hasShippableItems()  === false;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        return $cart->hasShippableItems() === false
            || ($cart->hasItems() &&
            $cart->getCarrier() instanceof CarrierInterface &&
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
                $formData = $form->getData();

                $cart->setCarrier($formData['carrier']);
                $cart->setComment($formData['comment']);
                $cart->save();
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
        $carriers = $this->shippableCarriersDiscovery->discoverCarriers($cart, $cart->getShippingAddress());
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
        $form = $this->formFactory->createNamed('', CarrierType::class, [
            'carrier' => $cart->getCarrier(),
            'comment' => $cart->getComment()
        ], [
            'carriers' => $carriers,
            'cart' => $cart
        ]);

        $form->add('comment', TextareaType::class, [
            'required' => false,
            'label' => 'coreshop.ui.comment'
        ]);

        if ($request->isMethod('post')) {
            $form = $form->handleRequest($request);
        }

        return $form;
    }
}
