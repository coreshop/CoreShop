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

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use CoreShop\Component\Core\Provider\DefaultTaxAddressProviderInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use Symfony\Component\Templating\Helper\Helper;

class ProductTaxHelper extends Helper implements ProductTaxHelperInterface
{
    /**
     * @var ProductPriceHelperInterface
     */
    private $priceHelper;

    /**
     * @var ShopperContextInterface
     */
    private $shopperContext;

    /**
     * @var ProductTaxCalculatorFactoryInterface
     */
    private $taxCalculatorFactory;

    /**
     * @var DefaultTaxAddressProviderInterface
     */
    private $defaultAddressProvider;

    /**
     * @param ProductPriceHelperInterface $priceHelper
     * @param ShopperContextInterface $shopperContext
     * @param ProductTaxCalculatorFactoryInterface $taxCalculatorFactory
     * @param DefaultTaxAddressProviderInterface $defaultAddressProvider
     */
    public function __construct(
        ProductPriceHelperInterface $priceHelper,
        ShopperContextInterface $shopperContext,
        ProductTaxCalculatorFactoryInterface $taxCalculatorFactory,
        DefaultTaxAddressProviderInterface $defaultAddressProvider
    )
    {
        $this->priceHelper = $priceHelper;
        $this->shopperContext = $shopperContext;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
        $this->defaultAddressProvider = $defaultAddressProvider;
    }

    /**
     * @param PurchasableInterface $product
     * @return array|int
     */
    public function getTaxAmount(PurchasableInterface $product)
    {
        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product, $this->defaultAddressProvider->getAddress($this->shopperContext->getContext()));
        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $taxCalculator->getTaxesAmount($this->priceHelper->getPrice($product, false));
        }
    }

    /**
     * @param PurchasableInterface $product
     * @return float
     */
    public function getTaxRate(PurchasableInterface $product)
    {
        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product, $this->defaultAddressProvider->getAddress($this->shopperContext->getContext()));
        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $taxCalculator->getTotalRate();
        }
    }

    public function getName()
    {
        return 'coreshop_product_tax';
    }
}
