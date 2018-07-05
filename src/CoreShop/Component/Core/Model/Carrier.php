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

use CoreShop\Component\Shipping\Model\Carrier as BaseCarrier;
use CoreShop\Component\Store\Model\StoresAwareTrait;

class Carrier extends BaseCarrier implements CarrierInterface
{
    use StoresAwareTrait {
        __construct as storesAwareConstructor;
    }

    /**
     * @var TaxRuleGroupInterface
     */
    private $taxRule;

    public function __construct()
    {
        parent::__construct();
        $this->storesAwareConstructor();
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRule()
    {
        return $this->taxRule;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxRule(TaxRuleGroupInterface $taxRule)
    {
        $this->taxRule = $taxRule;
    }
}
