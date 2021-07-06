<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Index\Condition;

class InCondition implements ConditionInterface
{
    private string $fieldName;
    private array $values;

    public function __construct(string $fieldName, array $values)
    {
        $this->fieldName = $fieldName;
        $this->values = $values;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function setFieldName($fieldName): void
    {
        $this->fieldName = $fieldName;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): void
    {
        $this->values = $values;
    }
}
