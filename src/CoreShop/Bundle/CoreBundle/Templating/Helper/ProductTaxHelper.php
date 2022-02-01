<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Templating\Helper;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
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
     * @param ProductPriceHelperInterface          $priceHelper
     * @param ShopperContextInterface              $shopperContext
     * @param ProductTaxCalculatorFactoryInterface $taxCalculatorFactory
     * @param DefaultTaxAddressProviderInterface   $defaultAddressProvider
     */
    public function __construct(
        ProductPriceHelperInterface $priceHelper,
        ShopperContextInterface $shopperContext,
        ProductTaxCalculatorFactoryInterface $taxCalculatorFactory,
        DefaultTaxAddressProviderInterface $defaultAddressProvider
    ) {
        $this->priceHelper = $priceHelper;
        $this->shopperContext = $shopperContext;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
        $this->defaultAddressProvider = $defaultAddressProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxAmount(PurchasableInterface $product, array $context = [])
    {
        if (empty($context)) {
            $context = $this->shopperContext->getContext();

            @trigger_error(
                'Calling getTaxAmount without a context is deprecated since 2.1.0 and will be removed with 2.2.0',
                E_USER_DEPRECATED
            );
        }

        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product, $this->defaultAddressProvider->getAddress($context));

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $taxCalculator->getTaxesAmount($this->priceHelper->getPrice($product, false, $context));
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRate(PurchasableInterface $product, array $context = [])
    {
        if (empty($context)) {
            $context = $this->shopperContext->getContext();

            @trigger_error(
                'Calling getTaxRate without a context is deprecated since 2.1.0 and will be removed with 2.2.0',
                E_USER_DEPRECATED
            );
        }

        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product, $this->defaultAddressProvider->getAddress($context));

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
