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

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Order\Calculator\PurchasableWholesalePriceCalculatorInterface;
use CoreShop\Component\Order\Exception\NoPurchasableWholesalePriceFoundException;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use Webmozart\Assert\Assert;

final class CartItemsWholesaleProcessor implements CartProcessorInterface
{
    /**
     * @var PurchasableWholesalePriceCalculatorInterface
     */
    private $wholesalePriceCalculator;

    /**
     * @param PurchasableWholesalePriceCalculatorInterface $wholesalePriceCalculator
     */
    public function __construct(PurchasableWholesalePriceCalculatorInterface $wholesalePriceCalculator)
    {
        $this->wholesalePriceCalculator = $wholesalePriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        $store = $cart->getStore();

        /**
         * @var StoreInterface $store
         */
        Assert::isInstanceOf($store, StoreInterface::class);

        $context = [
            'store' => $store,
            'customer' => $cart->getCustomer() ?: null,
            'currency' => $cart->getCurrency(),
            'country' => $store->getBaseCountry(),
            'cart' => $cart,
        ];

        /**
         * @var CartItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            $product = $item->getProduct();

            try {
                $item->setItemWholesalePrice(
                    $this->wholesalePriceCalculator->getPurchasableWholesalePrice($product, $context)
                );
            } catch (NoPurchasableWholesalePriceFoundException $ex) {
                $item->setItemWholesalePrice(0);
            }
        }
    }
}
