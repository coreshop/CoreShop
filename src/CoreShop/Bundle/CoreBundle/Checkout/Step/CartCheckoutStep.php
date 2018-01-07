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

use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\HttpFoundation\Request;

class CartCheckoutStep implements CheckoutStepInterface
{
    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'cart';
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
    public function getNextRoute(CartInterface $cart, Request $request)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        return count($cart->getItems()) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CartInterface $cart, Request $request)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CartInterface $cart, Request $request)
    {
        //nothing to do here
    }
}
