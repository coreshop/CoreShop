<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Bundle\CoreBundle\Templating\Helper\ProductPriceHelperInterface;
use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ProductPriceExtension extends AbstractExtension
{
    private $productPriceCalculator;

    public function __construct(TaxedProductPriceCalculatorInterface $productPriceCalculator)
    {
        $this->productPriceCalculator = $productPriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
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
