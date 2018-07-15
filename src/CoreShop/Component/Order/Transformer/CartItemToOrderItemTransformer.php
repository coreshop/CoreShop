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

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\ProposalItemInterface;
use Webmozart\Assert\Assert;

class CartItemToOrderItemTransformer extends AbstractCartItemToSaleItemTransformer
{
    /**
     * {@inheritdoc}
     */
    public function transform(ProposalInterface $order, ProposalItemInterface $cartItem, ProposalItemInterface $orderItem)
    {
        /**
         * @var $order OrderInterface
         * @var $cartItem CartItemInterface
         * @var $orderItem OrderItemInterface
         */
        Assert::isInstanceOf($cartItem, CartItemInterface::class);
        Assert::isInstanceOf($orderItem, OrderItemInterface::class);
        Assert::isInstanceOf($order, OrderInterface::class);

        return $this->transformSaleItem($order, $cartItem, $orderItem, 'order_item');
    }
}
