<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ProductPriceExtension extends AbstractExtension
{
    public function __construct(
        private TaxedProductPriceCalculatorInterface $productPriceCalculator,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('coreshop_product_price', [$this->productPriceCalculator, 'getPrice'], ['withTax' => ['with_tax']]),
            new TwigFilter('coreshop_product_retail_price', [$this->productPriceCalculator, 'getRetailPrice'], ['withTax' => ['with_tax']]),
            new TwigFilter('coreshop_product_discount_price', [$this->productPriceCalculator, 'getDiscountPrice'], ['withTax' => ['with_tax']]),
            new TwigFilter('coreshop_product_discount', [$this->productPriceCalculator, 'getDiscount'], ['withTax' => ['with_tax']]),
        ];
    }
}
