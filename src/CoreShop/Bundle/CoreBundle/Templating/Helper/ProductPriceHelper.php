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

namespace CoreShop\Bundle\CoreBundle\Templating\Helper;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Symfony\Component\Templating\Helper\Helper;

class ProductPriceHelper extends Helper implements ProductPriceHelperInterface
{
    private $productPriceCalculator;

    public function __construct(TaxedProductPriceCalculatorInterface $productPriceCalculator)
    {
        $this->productPriceCalculator = $productPriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(PurchasableInterface $product, bool $withTax = true, array $context = []): int
    {
        return $this->productPriceCalculator->getPrice($product, $context, $withTax);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountPrice(PurchasableInterface $product, bool $withTax = true, array $context = []): int
    {
        return $this->productPriceCalculator->getDiscountPrice($product, $context, $withTax);
    }

    /**
     * {@inheritdoc}
     */
    public function getRetailPrice(PurchasableInterface $product, bool $withTax = true, array $context = []): int
    {
        return $this->productPriceCalculator->getRetailPrice($product, $context, $withTax);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(PurchasableInterface $product, bool $withTax = true, array $context = []): int
    {
        return $this->productPriceCalculator->getDiscount($product, $context, $withTax);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_product_price';
    }
}
