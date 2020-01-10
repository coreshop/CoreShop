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
    /**
     * @var TaxedProductPriceCalculatorInterface
     */
    private $productPriceCalculator;

    /**
     * @var ShopperContextInterface
     */
    private $shopperContext;

    /**
     * @param TaxedProductPriceCalculatorInterface $productPriceCalculator
     * @param ShopperContextInterface              $shopperContext
     */
    public function __construct(
        TaxedProductPriceCalculatorInterface $productPriceCalculator,
        ShopperContextInterface $shopperContext
    ) {
        $this->productPriceCalculator = $productPriceCalculator;
        $this->shopperContext = $shopperContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(PurchasableInterface $product, $withTax = true)
    {
        return $this->productPriceCalculator->getPrice($product, $this->shopperContext->getContext(), $withTax);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountPrice(PurchasableInterface $product, $withTax = true)
    {
        return $this->productPriceCalculator->getDiscountPrice($product, $this->shopperContext->getContext(), $withTax);
    }

    /**
     * {@inheritdoc}
     */
    public function getRetailPrice(PurchasableInterface $product, $withTax = true)
    {
        return $this->productPriceCalculator->getRetailPrice($product, $this->shopperContext->getContext(), $withTax);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(PurchasableInterface $product, $withTax = true)
    {
        return $this->productPriceCalculator->getDiscount($product, $this->shopperContext->getContext(), $withTax);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_product_price';
    }
}
