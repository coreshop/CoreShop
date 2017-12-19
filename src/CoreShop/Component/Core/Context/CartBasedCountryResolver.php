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

namespace CoreShop\Component\Core\Context;

use CoreShop\Component\Address\Context\RequestBased\RequestResolverInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\HttpFoundation\Request;

final class CartBasedCountryResolver implements RequestResolverInterface
{
    /**
     * @var CartContextInterface
     */
    private $cartContext;

    public function __construct(CartContextInterface $cartContext)
    {
        $this->cartContext = $cartContext;
    }

    /**
     * {@inheritdoc}
     */
    public function findCountry(Request $request)
    {
        $cart = $this->cartContext->getCart();

        if ($cart instanceof CartInterface) {
            if ($cart->getShippingAddress() instanceof AddressInterface) {
                return $cart->getShippingAddress()->getCountry();
            }

            return $cart->getInvoiceAddress()->getCountry();
        }

        return null;
    }
}
