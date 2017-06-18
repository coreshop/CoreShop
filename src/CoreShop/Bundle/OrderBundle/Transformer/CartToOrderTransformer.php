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

namespace CoreShop\Bundle\OrderBundle\Transformer;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use Webmozart\Assert\Assert;

class CartToOrderTransformer extends AbstractCartToSaleTransformer
{
    /**
     * {@inheritdoc}
     */
    public function transform(ProposalInterface $cart, ProposalInterface $order, $exchangeRate)
    {
        /**
         * @var $cart CartInterface
         * @var $order OrderInterface
         */
        Assert::isInstanceOf($cart, CartInterface::class);
        Assert::isInstanceOf($order, OrderInterface::class);

        $order->setPaymentFee($cart->getPaymentFee(true), true);
        $order->setPaymentFee($cart->getPaymentFee(false), false);
        $order->setPaymentFeeTaxRate($cart->getPaymentFeeTaxRate());

        $order = $this->transformSale($cart, $order, 'order', $exchangeRate);

        $cart->setOrder($order);
        $cart->save();

        return $order;
    }
}
