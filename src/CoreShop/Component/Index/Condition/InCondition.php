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

class InCondition implements ConditionInterface
{
    private string $fieldName;

    private array $values;

    public function __construct(
        string $fieldName,
        array $values,
    ) {
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
