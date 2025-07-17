<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Taxation\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;

interface TaxRuleInterface extends ResourceInterface, TimestampableInterface
{
    public function getId(): ?int;

    /**
     * @return int
     */
    public function getBehavior();

    /**
     * @param int $behavior
     */
    public function setBehavior($behavior);

    /**
     * @return TaxRuleGroupInterface|null
     */
    public function getTaxRuleGroup();

    public function setTaxRuleGroup(?TaxRuleGroupInterface $taxRuleGroup);

    /**
     * @return TaxRateInterface
     */
    public function getTaxRate();

    public function setTaxRate(TaxRateInterface $taxRate);
}
