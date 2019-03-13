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

namespace CoreShop\Bundle\CoreBundle\Templating\Helper;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use Symfony\Component\Templating\Helper\Helper;

class ProductUnitDefinitionsHelper extends Helper implements ProductUnitDefinitionsHelperInterface
{
    /**
     * @var ShopperContextInterface
     */
    protected $shopperContext;

    /**
     * @var TaxedProductPriceCalculatorInterface
     */
    protected $productPriceCalculator;

    /**
     * @param ShopperContextInterface         $shopperContext
     * @param TaxedProductPriceCalculatorInterface $productPriceCalculator
     */
    public function __construct(ShopperContextInterface $shopperContext, TaxedProductPriceCalculatorInterface $productPriceCalculator)
    {
        $this->shopperContext = $shopperContext;
        $this->productPriceCalculator = $productPriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAvailableUnitDefinitions(ProductInterface $product)
    {
        return $product->hasUnitDefinitions();
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalUnitDefinitionsWithPrices(ProductInterface $product, $withTax = true)
    {
        if (!$product->hasUnitDefinitions()) {
            return [];
        }

        $data = [];
        $context = $this->shopperContext->getContext();

        foreach ($product->getUnitDefinitions()->getAdditionalUnitDefinitions() as $unitDefinition) {

            $context['unitDefinition'] = $unitDefinition;

            $data[] = [
                'definition' => $unitDefinition,
                'price'      => $this->productPriceCalculator->getPrice($product, $context, $withTax)
            ];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_product_unit_definitions';
    }
}
