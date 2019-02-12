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

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\ProductQuantityPriceRules\Detector\QuantityReferenceDetectorInterface;
use CoreShop\Component\ProductQuantityPriceRules\Exception\NoRuleFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use Symfony\Component\Templating\Helper\Helper;

class ProductQuantityPriceHelper extends Helper implements ProductQuantityPriceHelperInterface
{
    /**
     * @var ShopperContextInterface
     */
    protected $shopperContext;

    /**
     * @var ProductPriceCalculatorInterface
     */
    protected $productPriceCalculator;

    /**
     * @var QuantityReferenceDetectorInterface
     */
    protected $quantityReferenceDetector;

    /**
     * @param ShopperContextInterface            $shopperContext
     * @param ProductPriceCalculatorInterface    $productPriceCalculator
     * @param QuantityReferenceDetectorInterface $quantityReferenceDetector
     */
    public function __construct(
        ShopperContextInterface $shopperContext,
        ProductPriceCalculatorInterface $productPriceCalculator,
        QuantityReferenceDetectorInterface $quantityReferenceDetector
    ) {
        $this->shopperContext = $shopperContext;
        $this->productPriceCalculator = $productPriceCalculator;
        $this->quantityReferenceDetector = $quantityReferenceDetector;
    }

    /**
     * {@inheritdoc}
     */
    public function hasActiveQuantityPriceRuleRanges(QuantityRangePriceAwareInterface $product)
    {
        try {
            $this->quantityReferenceDetector->detectRule($product, $this->shopperContext->getContext());
        } catch (NoRuleFoundException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantityPriceRule(QuantityRangePriceAwareInterface $product)
    {
        return $this->quantityReferenceDetector->detectRule($product, $this->shopperContext->getContext());
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantityPriceRuleRanges(QuantityRangePriceAwareInterface $product)
    {
        $productQuantityPriceRule = $this->quantityReferenceDetector->detectRule($product, $this->shopperContext->getContext());

        return $productQuantityPriceRule->getRanges();
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantityPriceRuleRangePrice(QuantityRangeInterface $range, QuantityRangePriceAwareInterface $product)
    {
        $realItemPrice = $this->productPriceCalculator->getPrice($product, $this->shopperContext->getContext(), true);
        $quantityPrice = $this->quantityReferenceDetector->detectRangePrice($product, $range, $realItemPrice, $this->shopperContext->getContext());

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
