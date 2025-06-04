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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Taxation\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use CoreShop\Component\Resource\Model\ToggleableInterface;
use Doctrine\Common\Collections\Collection;

interface TaxRuleGroupInterface extends ResourceInterface, TimestampableInterface, ToggleableInterface
{
    public function getId(): ?int;

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return Collection|TaxRuleInterface[]
     */
    public function getTaxRules();

    /**
     * @return bool
     */
    public function hasTaxRules();

    public function addTaxRule(TaxRuleInterface $taxRule);

    public function removeTaxRule(TaxRuleInterface $taxRule);

    /**
     * @return bool
     */
    public function hasTaxRule(TaxRuleInterface $taxRule);
}
