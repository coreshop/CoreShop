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

namespace CoreShop\Component\Index\Condition;

class RangeCondition implements ConditionInterface
{
    public function __construct(private string $fieldName, private ?float $from, private ?float $to)
    {
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
