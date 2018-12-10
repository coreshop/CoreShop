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

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\TokenGenerator\UniqueTokenGenerator;
use Webmozart\Assert\Assert;

class CartToOrderTransformer extends AbstractCartToSaleTransformer
{
    /**
     * {@inheritdoc}
     */
    public function transform(ProposalInterface $cart, ProposalInterface $order)
    {
        /**
         * @var $cart  CartInterface
         * @var $order OrderInterface
         */
        Assert::isInstanceOf($cart, CartInterface::class);
        Assert::isInstanceOf($order, OrderInterface::class);

        $tokenGenerator = new UniqueTokenGenerator();
        $order->setPaymentProvider($cart->getPaymentProvider());
        $order->setToken($tokenGenerator->generate(10));

        $order = $this->transformSale($cart, $order, 'order');

        if ($cart->getId()) {
            $cart->setOrder($order);

            VersionHelper::useVersioning(function () use ($cart) {
                $cart->save();
            }, false);
        }

        return $order;
    }
}
