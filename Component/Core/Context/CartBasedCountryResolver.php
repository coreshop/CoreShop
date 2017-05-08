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
 *
*/

namespace CoreShop\Component\Core\Context;

use CoreShop\Component\Address\Context\RequestBased\RequestResolverInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\HttpFoundation\Request;

final class CartBasedCountryResolver implements RequestResolverInterface
{
    /**
     * @var CartManagerInterface
     */
    private $cartManager;

    public function __construct(CartManagerInterface $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    /**
     * {@inheritdoc}
     */
    public function findCountry(Request $request)
    {
        $cart = $this->cartManager->getCart();

        if ($cart instanceof CartInterface) {
            if ($cart->getShippingAddress() instanceof AddressInterface) {
                return $cart->getShippingAddress()->getCountry();
            }

            return $cart->getInvoiceAddress()->getCountry();
        }

        return null;
    }
}
