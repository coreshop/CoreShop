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

interface TaxItemInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(?string $name);

    public function getRate(): ?float;

    public function setRate(?float $rate);

    public function getAmount(): int;

    public function setAmount(int $amount);

    public function getTaxRate(): ?TaxRateInterface;

    public function setTaxRate(TaxRateInterface $taxRate);
}
