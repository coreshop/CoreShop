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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Order\Model\Cart as BaseCart;

use CoreShop\Component\Shipping\Model\CarrierAwareTrait;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

class Cart extends BaseCart implements CartInterface
{
    use CarrierAwareTrait;

    /**
     * {@inheritdoc}
     *
     * TODO: Do we actually need the container here?
     * Can't we just save shipping via shipping step?
     */
    public function getShipping($withTax = true)
    {
        if ($this->getCarrier() instanceof CarrierInterface) {
            return $this->getContainer()->get('coreshop.carrier.price_calculator.default')->getPrice($this->getCarrier(), $this, $this->getShippingAddress(), $withTax);
        }

        return 0;
    }

    /**
     *  {@inheritdoc}
     */
    public function getShippingTaxRate()
    {
        if ($this->getCarrier() instanceof CarrierInterface && $this->getCarrier()->getTaxRule() instanceof TaxRuleGroupInterface) {
            $taxCalculator = $this->getContainer()->get('coreshop.taxation.factory.tax_calculator')->getTaxCalculatorForAddress($this->getCarrier()->getTaxRule(), $this->getShippingAddress());

            if ($taxCalculator instanceof TaxCalculatorInterface) {
                return $taxCalculator->getTotalRate();
            }
        }

        return 0;
    }

    /**
     * calculates the total without discount.
     *
     * @param bool $withTax
     *
     * @return float
     */
    protected function getTotalWithoutDiscount($withTax = true)
    {
        return parent::getTotalWithoutDiscount($withTax) + $this->getShipping($withTax);
    }
}
