<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use CoreShop\Component\Core\Taxation\TaxApplicatorInterface;
use CoreShop\Component\Order\Calculator\PurchasablePriceCalculatorInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

final class ProductTaxExtension extends \Twig_Extension
{
    /**
     * @var TaxedProductPriceCalculatorInterface
     */
    private $priceCalculator;

    /**
     * @var ProductTaxCalculatorFactoryInterface
     */
    private $taxCalculatorFactory;

    /**
     * @param TaxedProductPriceCalculatorInterface  $priceCalculator
     * @param ProductTaxCalculatorFactoryInterface $taxCalculatorFactory
     */
    public function __construct(
        TaxedProductPriceCalculatorInterface $priceCalculator,
        ProductTaxCalculatorFactoryInterface $taxCalculatorFactory
    ) {
        $this->priceCalculator = $priceCalculator;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_Filter('coreshop_product_tax_amount', [$this, 'getTaxAmount']),
            new \Twig_Filter('coreshop_product_tax_rate', [$this, 'getTaxRate']),
        ];
    }

    /**
     * @param PurchasableInterface $product
     * @return array|int
     */
    public function getTaxAmount(PurchasableInterface $product)
    {
        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product);
        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $taxCalculator->getTaxesAmount($this->priceCalculator->getPrice($product, false));
        }
    }

    /**
     * @param PurchasableInterface $product
     * @return float
     */
    public function getTaxRate(PurchasableInterface $product)
    {
        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product);
        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $taxCalculator->getTotalRate();
        }
    }
}
