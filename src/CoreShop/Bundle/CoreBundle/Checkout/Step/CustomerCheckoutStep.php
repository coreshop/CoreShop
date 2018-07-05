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

use CoreShop\Bundle\CoreBundle\Customer\CustomerAlreadyExistsException;
use CoreShop\Bundle\CoreBundle\Customer\RegistrationServiceInterface;
use CoreShop\Bundle\CoreBundle\Form\Type\GuestRegistrationType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Checkout\ValidationCheckoutStepInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomerCheckoutStep implements CheckoutStepInterface, ValidationCheckoutStepInterface
{
    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var RegistrationServiceInterface
     */
    private $registrationService;

    /**
     * @param CustomerContextInterface     $customerContext
     * @param FormFactoryInterface         $formFactory
     * @param RegistrationServiceInterface $registrationService
     */
    public function __construct(CustomerContextInterface $customerContext, FormFactoryInterface $formFactory, RegistrationServiceInterface $registrationService)
    {
        $this->customerContext = $customerContext;
        $this->formFactory = $formFactory;
        $this->registrationService = $registrationService;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'customer';
    }

    /**
     * {@inheritdoc}
     */
    public function doAutoForward(CartInterface $cart)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        if (!$cart->hasItems()) {
            return false;
        }

        try {
            $customer = $this->customerContext->getCustomer();

            return $customer instanceof CustomerInterface;
        } catch (CustomerNotFoundException $ex) {
            //If we don't have a customer, we ignore the exception and return false
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CartInterface $cart, Request $request)
    {
        $form = $this->createForm($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $customer = $formData['customer'];
            $address = $formData['address'];

            if (!$customer instanceof CustomerInterface ||
                !$address instanceof AddressInterface
            ) {
                return false;
            }

            try {
                $this->registrationService->registerCustomer($customer, $address, $formData, true);
            } catch (CustomerAlreadyExistsException $e) {
                throw new CheckoutException('Customer already exists', 'coreshop.ui.error.customer_already_exists');
            }

            return true;
        }

        if (!$this->validate($cart)) {
            throw new CheckoutException('no customer found', 'coreshop.ui.error.coreshop_checkout_customer_invalid');
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CartInterface $cart, Request $request)
    {
        return [
            'guestForm' => $this->createForm($request)->createView(),
        ];
    }

    /**
     * @param Request $request
     *
     * @return FormInterface
     */
    private function createForm(Request $request)
    {
        $view = $this->formFactory->createNamed('guest', GuestRegistrationType::class);

        $handledView = $view->handleRequest($request);

        return $handledView;
    }
}
