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

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\Templating\Helper;

use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use CoreShop\Component\ProductQuantityPriceRules\Detector\QuantityReferenceDetectorInterface;
use CoreShop\Component\ProductQuantityPriceRules\Exception\NoRuleFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use Symfony\Component\Templating\Helper\Helper;

class ProductQuantityPriceHelper extends Helper implements ProductQuantityPriceHelperInterface
{
    /**
     * @var TaxedProductPriceCalculatorInterface
     */
    protected $productPriceCalculator;

    /**
     * @var QuantityReferenceDetectorInterface
     */
    protected $quantityReferenceDetector;

    /**
     * @param TaxedProductPriceCalculatorInterface $productPriceCalculator
     * @param QuantityReferenceDetectorInterface   $quantityReferenceDetector
     */
    public function __construct(
        TaxedProductPriceCalculatorInterface $productPriceCalculator,
        QuantityReferenceDetectorInterface $quantityReferenceDetector
    ) {
        $this->productPriceCalculator = $productPriceCalculator;
        $this->quantityReferenceDetector = $quantityReferenceDetector;
    }

    /**
     * {@inheritdoc}
     */
    public function hasActiveQuantityPriceRuleRanges(QuantityRangePriceAwareInterface $product, array $context)
    {
        try {
            $this->quantityReferenceDetector->detectRule($product, $context);
        } catch (NoRuleFoundException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantityPriceRule(QuantityRangePriceAwareInterface $product, array $context)
    {
        return $this->quantityReferenceDetector->detectRule($product, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantityPriceRuleRanges(QuantityRangePriceAwareInterface $product, array $context)
    {
        $productQuantityPriceRule = $this->quantityReferenceDetector->detectRule($product, $context);

        return $productQuantityPriceRule->getRanges();
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantityPriceRuleRangePrice(
        QuantityRangeInterface $range,
        QuantityRangePriceAwareInterface $product,
        array $context,
        bool $withTax = true,
        array $additionalContext = null
    ) {
        if (is_array($additionalContext)) {
            $context = array_merge($context, $additionalContext);
        }

        $realItemPrice = $this->productPriceCalculator->getPrice($product, $context, $withTax);
        $quantityPrice = $this->quantityReferenceDetector->detectRangePrice($product, $range, $realItemPrice, $context);

        return $quantityPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_product_quantity_price';
    }
}
