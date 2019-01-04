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

namespace CoreShop\Component\Core\Product;

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
     * @param TaxCalculatorFactoryInterface $taxCalculatorFactory
     * @param PimcoreFactoryInterface       $addressFactory
     */
    public function __construct(TaxCalculatorFactoryInterface $taxCalculatorFactory, PimcoreFactoryInterface $addressFactory)
    {
        $this->taxCalculatorFactory = $taxCalculatorFactory;
        $this->addressFactory = $addressFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxCalculator(PurchasableInterface $product, AddressInterface $address)
    {
        $taxRuleGroup = $product->getTaxRule();

        if ($taxRuleGroup instanceof TaxRuleGroupInterface) {
            return $this->taxCalculatorFactory->getTaxCalculatorForAddress($taxRuleGroup, $address);
        }

        return null;
    }
}
