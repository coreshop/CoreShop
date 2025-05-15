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

namespace CoreShop\Component\Index\Condition;

class RangeCondition implements ConditionInterface
{
    public function __construct(
        private string $fieldName,
        private ?float $from,
        private ?float $to,
    ) {
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function setFieldName(string $fieldName): void
    {
        $this->fieldName = $fieldName;
    }

    public function getFrom(): ?float
    {
        return $this->from;
    }

    public function setFrom(?float $from): void
    {
        $this->from = $from;
    }

    public function getTo(): ?float
    {
        return $this->to;
    }

    public function setTo(?float $to): void
    {
        $this->to = $to;
    }
}
