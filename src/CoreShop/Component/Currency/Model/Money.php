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

namespace CoreShop\Component\Currency\Model;

class Money implements \Stringable
{
    public function __construct(
        public int $value,
        public CurrencyInterface $currency,
    ) {
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
    }

    public function setCurrency(CurrencyInterface $currency): void
    {
        $this->currency = $currency;
    }

    public function __toString(): string
    {
        return sprintf('%s%s', $this->value, $this->currency->getIsoCode());
    }
}
