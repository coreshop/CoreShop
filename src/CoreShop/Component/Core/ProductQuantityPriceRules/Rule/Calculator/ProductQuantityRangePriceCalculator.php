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

namespace CoreShop\Component\Core\ProductQuantityPriceRules\Rule\Calculator;

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Order\Calculator\PurchasableCalculatorInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\ProductQuantityPriceRules\Locator\QuantityRangePriceLocatorInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use CoreShop\Component\ProductQuantityPriceRules\Rule\Action\ProductQuantityPriceRuleActionInterface;
use CoreShop\Component\ProductQuantityPriceRules\Rule\Calculator\ProductQuantityRangePriceCalculatorInterface as BaseProductQuantityRangePriceCalculatorInterface;
use Webmozart\Assert\Assert;

final class ProductQuantityRangePriceCalculator implements ProductQuantityRangePriceCalculatorInterface
{
    /**
     * @var BaseProductQuantityRangePriceCalculatorInterface
     */
    protected $inner;

    /**
     * @var ServiceRegistryInterface
     */
    protected $actionRegistry;

    /**
     * @var PurchasableCalculatorInterface
     */
    protected $productPriceCalculator;

    /**
     * @var QuantityRangePriceLocatorInterface
     */
    protected $quantityRangePriceLocator;

    /**
     * @param BaseProductQuantityRangePriceCalculatorInterface $inner
     * @param ServiceRegistryInterface                         $actionRegistry
     * @param PurchasableCalculatorInterface                   $productPriceCalculator
     * @param QuantityRangePriceLocatorInterface               $quantityRangePriceLocator
     */
    public function __construct(
        BaseProductQuantityRangePriceCalculatorInterface $inner,
        ServiceRegistryInterface $actionRegistry,
        PurchasableCalculatorInterface $productPriceCalculator,
        QuantityRangePriceLocatorInterface $quantityRangePriceLocator
    ) {
        $this->inner = $inner;
        $this->actionRegistry = $actionRegistry;
        $this->productPriceCalculator = $productPriceCalculator;
        $this->quantityRangePriceLocator = $quantityRangePriceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantityPriceRulesForProduct(QuantityRangePriceAwareInterface $subject, array $context)
    {
        return $this->inner->getQuantityPriceRulesForProduct($subject, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantityRangePriceForCartItem(QuantityRangePriceAwareInterface $subject, CartItemInterface $cartItem, array $context)
    {
        $quantityPriceRules = $this->getQuantityPriceRulesForProduct($subject, $context);

        if (!is_array($quantityPriceRules)) {
            return false;
        }

        if (count($quantityPriceRules) === 0) {
            return false;
        }

        $quantityPriceRule = $quantityPriceRules[0];
        $locatedRangePrice = $this->quantityRangePriceLocator->locate($quantityPriceRule->getRanges(), $cartItem->getQuantity());

        if (!$locatedRangePrice instanceof QuantityRangeInterface) {
            return false;
        }

        $price = $this->calculateRangePrice($locatedRangePrice, $subject, $context);

        return !is_numeric($price) || $price === 0 ? false : $price;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateRangePrice(QuantityRangeInterface $range, QuantityRangePriceAwareInterface $subject, array $context)
    {
        $realItemPrice = 0;

        if ($subject instanceof PurchasableInterface) {
            $realItemPrice = $this->productPriceCalculator->getPrice($subject, $context, true);
        }
        $pricingBehaviour = $range->getPricingBehaviour();

        Assert::isInstanceOf($context['currency'], CurrencyInterface::class);

        /**
         * @var ProductQuantityPriceRuleActionInterface $service
         */
        $service = $this->actionRegistry->get($pricingBehaviour);

        return $service->calculate($range, $subject, $realItemPrice, $context);
    }
}
