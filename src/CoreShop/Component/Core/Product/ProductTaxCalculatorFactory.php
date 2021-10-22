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

declare(strict_types=1);

namespace CoreShop\Component\Core\Product;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;

class ProductTaxCalculatorFactory implements ProductTaxCalculatorFactoryInterface
{
    public function __construct(private TaxCalculatorFactoryInterface $taxCalculatorFactory)
    {
    }

    public function getTaxCalculator(PurchasableInterface $product, AddressInterface $address): ?TaxCalculatorInterface
    {
        $taxRuleGroup = $product->getTaxRule();

        if ($taxRuleGroup instanceof TaxRuleGroupInterface) {
            return $this->taxCalculatorFactory->getTaxCalculatorForAddress($taxRuleGroup, $address);
        }

        return null;
    }
}
