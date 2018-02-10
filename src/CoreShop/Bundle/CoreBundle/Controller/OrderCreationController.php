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

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\OrderBundle\Controller\OrderCreationController as BaseOrderCreationController;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

class OrderCreationController extends BaseOrderCreationController
{
    use CoreSaleCreationTrait;

    protected function prepareCart(Request $request, CartInterface $cart)
    {
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\CartInterface::class);

        /**
         * @var $cart \CoreShop\Component\Core\Model\CartInterface
         */
        $carrierId = $request->get('carrier');

        if ($carrierId) {
            $carrier = $this->get('coreshop.repository.carrier')->find($carrierId);

            if (!$carrier instanceof CarrierInterface) {
                throw new \InvalidArgumentException("Carrier with ID '$carrierId' not found");
            }

            $cart->setCarrier($carrier);
        }
    }
}
