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

namespace CoreShop\Component\Core\Product;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;

class ProductTaxCalculatorFactory implements ProductTaxCalculatorFactoryInterface
{
    /**
     * @var TaxCalculatorFactoryInterface
     */
    private $taxCalculatorFactory;

    /**
     * @var PimcoreFactoryInterface
     */
    private $addressFactory;

    /**
     * @var CountryContextInterface
     */
    private $countryContext;

    /**
     * @param TaxCalculatorFactoryInterface $taxCalculatorFactory
     * @param PimcoreFactoryInterface $addressFactory
     * @param CountryContextInterface $countryContext
     */
    public function __construct(TaxCalculatorFactoryInterface $taxCalculatorFactory, PimcoreFactoryInterface $addressFactory, CountryContextInterface $countryContext)
    {
        $this->taxCalculatorFactory = $taxCalculatorFactory;
        $this->addressFactory = $addressFactory;
        $this->countryContext = $countryContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxCalculator(PurchasableInterface $product, AddressInterface $address = null)
    {
        $taxRuleGroup = $product->getTaxRule();

        if ($taxRuleGroup instanceof TaxRuleGroupInterface) {
            if (null === $address) {
                $address = $this->addressFactory->createNew();
                $country = $this->countryContext->getCountry();

                $address->setCountry($country);
            }

            return $this->taxCalculatorFactory->getTaxCalculatorForAddress($taxRuleGroup, $address);
        }

        throw new \InvalidArgumentException(sprintf('Product %s has no valid TaxRuleGroup', $product->getId()));
    }
}
