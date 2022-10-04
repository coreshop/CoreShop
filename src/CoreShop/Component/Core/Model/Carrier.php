<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Shipping\Model\Carrier as BaseCarrier;
use CoreShop\Component\Store\Model\StoresAwareTrait;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;

/**
 * @psalm-suppress MissingConstructor
 */
class Carrier extends BaseCarrier implements CarrierInterface, \Stringable
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

    /**
     * @return void
     */
    public function setTaxRule(TaxRuleGroupInterface $taxRule)
    {
        $this->taxRule = $taxRule;
    }

    public function __toString(): string
    {
        return sprintf('%s', $this->getIdentifier());
    }
}
