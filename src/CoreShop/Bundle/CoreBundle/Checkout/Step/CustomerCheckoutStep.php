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

use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomerCheckoutStep implements CheckoutStepInterface
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
     * @param CustomerContextInterface $customerContext
     * @param FormFactoryInterface     $formFactory
     */
    public function __construct(CustomerContextInterface $customerContext, FormFactoryInterface $formFactory)
    {
        $this->customerContext = $customerContext;
        $this->formFactory = $formFactory;
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
    public function doAutoForward()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        try {
            $customer = $this->customerContext->getCustomer();

            return $cart->hasItems() && $customer instanceof CustomerInterface;
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
        if (!$this->validate($cart)) {
            throw new CheckoutException('no customer found', 'coreshop_checkout_customer_invalid');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CartInterface $cart)
    {
        return [
        ];
    }
}
