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

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Bundle\CoreBundle\Customer\CustomerAlreadyExistsException;
use CoreShop\Bundle\CoreBundle\Customer\CustomerManagerInterface;
use CoreShop\Bundle\CoreBundle\Customer\RegistrationServiceInterface;
use CoreShop\Bundle\CoreBundle\Form\Type\GuestRegistrationType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Checkout\ValidationCheckoutStepInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

class CustomerCheckoutStep implements CheckoutStepInterface, ValidationCheckoutStepInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var CustomerManagerInterface
     */
    private $customerManager;

    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @param FormFactoryInterface $formFactory
     * @param CustomerManagerInterface $customerManager
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        CustomerManagerInterface $customerManager,
        LocaleContextInterface $localeContext
    ) {
        $this->formFactory = $formFactory;
        $this->customerManager = $customerManager;
        $this->localeContext = $localeContext;
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
        $customer = $cart->getCustomer();

        /**
         * @var CustomerInterface $customer
         */
        Assert::isInstanceOf($customer, CustomerInterface::class);

        return $customer->getUser();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        if (!$cart->hasItems()) {
            return false;
        }

        return $cart->getCustomer() instanceof CustomerInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CartInterface $cart, Request $request)
    {
        $form = $this->createForm($request, $cart);

        if ($form->isSubmitted() && $form->isValid()) {
            $customer = $form->getData();
            $customer->setLocaleCode($this->localeContext->getLocaleCode());

            $this->customerManager->persistCustomer($customer);

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
     * @param Request       $request
     * @param CartInterface $cart
     * @return FormInterface
     */
    private function createForm(Request $request, CartInterface $cart)
    {
        $form = $this->formFactory->createNamed('guest', GuestRegistrationType::class, $cart->getCustomer());

        return $form->handleRequest($request);
    }
}
