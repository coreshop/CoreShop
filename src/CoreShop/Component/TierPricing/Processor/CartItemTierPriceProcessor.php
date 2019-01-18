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

namespace CoreShop\Component\TierPricing\Processor;

use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Order\Calculator\PurchasableCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\TierPricing\Locator\TierPriceLocatorInterface;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRangeInterface;
use Webmozart\Assert\Assert;

class CartItemTierPriceProcessor implements CartProcessorInterface
{
    /**
     * @var CartProcessorInterface
     */
    protected $innerCartProcessor;

    /**
     * @var TierPriceLocatorInterface
     */
    private $tierPriceLocator;

    /**
     * @var PurchasableCalculatorInterface
     */
    private $productPriceCalculator;

    /**
     * @param CartProcessorInterface         $innerCartProcessor
     * @param TierPriceLocatorInterface      $tierPriceLocator
     * @param PurchasableCalculatorInterface $productPriceCalculator
     */
    public function __construct(
        CartProcessorInterface $innerCartProcessor,
        TierPriceLocatorInterface $tierPriceLocator,
        PurchasableCalculatorInterface $productPriceCalculator
    ) {
        $this->innerCartProcessor = $innerCartProcessor;
        $this->tierPriceLocator = $tierPriceLocator;
        $this->productPriceCalculator = $productPriceCalculator;
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
            'store'    => $store,
            'customer' => $cart->getCustomer() ?: null,
            'currency' => $cart->getCurrency(),
            'country'  => $store->getBaseCountry(),
            'cart'     => $cart,
        ];

        /**
         * @var CartItemInterface $item
         */
        foreach ($cart->getItems() as $item) {

            $tierItemPrice = $this->tierPriceLocator->locate($item->getProduct(), $store, $item->getQuantity());
            if ($tierItemPrice instanceof ProductTierPriceRangeInterface) {
                $itemPrice = $tierItemPrice->getPrice();
            } else {
                $itemPrice = $this->productPriceCalculator->getPrice($item->getProduct(), $context, true);
            }

            $this->innerCartProcessor->processItem($item, $itemPrice, $context);

        }
    }
}