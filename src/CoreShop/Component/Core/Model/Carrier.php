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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Shipping\Model\Carrier as BaseCarrier;
use CoreShop\Component\Store\Model\StoresAwareTrait;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;

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

    public function getTaxRule()
    {
        return $this->taxRule;
    }

    public function setTaxRule(TaxRuleGroupInterface $taxRule)
    {
        $this->taxRule = $taxRule;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s', $this->getIdentifier());
    }
}
