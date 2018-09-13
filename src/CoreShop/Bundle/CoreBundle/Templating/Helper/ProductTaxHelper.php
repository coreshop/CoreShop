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

namespace CoreShop\Bundle\CoreBundle\Templating\Helper;

use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use CoreShop\Component\Core\Provider\DefaultTaxAddressProviderInterface;
use CoreShop\Component\Order\Calculator\PurchasableCalculatorInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use Symfony\Component\Templating\Helper\Helper;

class ProductTaxHelper extends Helper implements ProductTaxHelperInterface
{
    /**
     * @var PurchasableCalculatorInterface
     */
    private $priceCalculator;

    /**
     * @var ProductTaxCalculatorFactoryInterface
     */
    private $taxCalculatorFactory;

    /**
     * @var DefaultTaxAddressProviderInterface
     */
    private $defaultTaxAddressProvider;

    /**
     * @param PurchasableCalculatorInterface $priceCalculator
     * @param ProductTaxCalculatorFactoryInterface $taxCalculatorFactory
     * @param DefaultTaxAddressProviderInterface   $defaultTaxAddressProvider
     */
    public function __construct(
        PurchasableCalculatorInterface $priceCalculator,
        ProductTaxCalculatorFactoryInterface $taxCalculatorFactory,
        DefaultTaxAddressProviderInterface $defaultTaxAddressProvider
    ) {
        $this->priceCalculator = $priceCalculator;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
        $this->defaultTaxAddressProvider = $defaultTaxAddressProvider;
    }

    /**
     * @param PurchasableInterface $product
     * @return array|int
     */
    public function getTaxAmount(PurchasableInterface $product)
    {
        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator(
            $product,
            $this->defaultTaxAddressProvider->getAddress()
        );

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $taxCalculator->getTaxesAmount($this->priceCalculator->getPrice($product, true));
        }

        return 0;
    }

    /**
     * @param PurchasableInterface $product
     * @return float
     */
    public function getTaxRate(PurchasableInterface $product)
    {
        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator(
            $product,
            $this->defaultTaxAddressProvider->getAddress()
        );

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $taxCalculator->getTotalRate();
        }

        return 0;
    }

    public function getName()
    {
        return 'coreshop_product_tax';
    }
}
