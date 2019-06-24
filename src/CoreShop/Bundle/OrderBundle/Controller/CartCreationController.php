<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Component\Order\Model\CartInterface;
use Pimcore\Model\DataObject\Concrete;

class CartCreationController extends AbstractCartCreationController
{
    /**
     * {@inheritdoc}
     */
    protected function persistCart(CartInterface $cart)
    {
        $cart->setKey(uniqid());
        $cart->setPublished(true);

        foreach ($cart->getItems() as $item) {
            if ($item instanceof Concrete) {
                $item->setKey(uniqid());
                $item->setPublished(true);
            }
        }

        $this->get('coreshop.cart.manager')->persistCart($cart);

        return [
            'success' => true,
            'id' => $cart->getId(),
        ];
    }
}
