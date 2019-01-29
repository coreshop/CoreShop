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

namespace CoreShop\Component\Core\TierPricing\Rule\Calculator;

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Order\Calculator\PurchasableCalculatorInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\TierPricing\Locator\TierPriceLocatorInterface;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRangeInterface;
use CoreShop\Component\TierPricing\Model\TierPriceAwareInterface;
use CoreShop\Component\TierPricing\Rule\Action\TierPriceActionInterface;
use \CoreShop\Component\TierPricing\Rule\Calculator\ProductTierPriceCalculatorInterface as BaseProductTierPriceCalculatorInterface;
use Webmozart\Assert\Assert;

final class ProductTierPriceCalculator implements ProductTierPriceCalculatorInterface
{
    /**
     * @var BaseProductTierPriceCalculatorInterface
     */
    private $inner;

    /**
     * @var ServiceRegistryInterface
     */
    private $actionRegistry;

    /**
     * @var PurchasableCalculatorInterface
     */
    private $productPriceCalculator;

    /**
     * @var TierPriceLocatorInterface
     */
    private $tierPriceLocator;

    /**
     * @param BaseProductTierPriceCalculatorInterface $inner
     * @param ServiceRegistryInterface            $actionRegistry
     * @param PurchasableCalculatorInterface      $productPriceCalculator
     * @param TierPriceLocatorInterface           $tierPriceLocator
     */
    public function __construct(
        BaseProductTierPriceCalculatorInterface $inner,
        ServiceRegistryInterface $actionRegistry,
        PurchasableCalculatorInterface $productPriceCalculator,
        TierPriceLocatorInterface $tierPriceLocator
    ) {
        $this->inner = $inner;
        $this->actionRegistry = $actionRegistry;
        $this->productPriceCalculator = $productPriceCalculator;
        $this->tierPriceLocator = $tierPriceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function getTierPriceRulesForProduct(TierPriceAwareInterface $subject, array $context)
    {
        return $this->inner->getTierPriceRulesForProduct($subject, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getTierPriceForCartItem(TierPriceAwareInterface $subject, CartItemInterface $cartItem, array $context)
    {
        $tierPriceRules = $this->getTierPriceRulesForProduct($subject, $context);

        if (!is_array($tierPriceRules)) {
            return false;
        }

        if (count($tierPriceRules) === 0) {
            return false;
        }

        $tierPriceRule = $tierPriceRules[0];
        $locatedTierPrice = $this->tierPriceLocator->locate($tierPriceRule->getRanges(), $cartItem->getQuantity());

        if (!$locatedTierPrice instanceof ProductTierPriceRangeInterface) {
            return false;
        }

        $price = $this->calculateRangePrice($locatedTierPrice, $subject, $context);

        return !is_numeric($price) || $price === 0 ? false : $price;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateRangePrice(ProductTierPriceRangeInterface $range, TierPriceAwareInterface $subject, array $context)
    {
        $realItemPrice = 0;

        if ($subject instanceof PurchasableInterface) {
            $realItemPrice = $this->productPriceCalculator->getPrice($subject, $context, true);
        }
        $pricingBehaviour = $range->getPricingBehaviour();

        Assert::isInstanceOf($context['currency'], CurrencyInterface::class);

        /**
         * @var TierPriceActionInterface $service
         */
        $service = $this->actionRegistry->get($pricingBehaviour);

        return $service->calculate($range, $subject, $realItemPrice, $context);
    }
}
