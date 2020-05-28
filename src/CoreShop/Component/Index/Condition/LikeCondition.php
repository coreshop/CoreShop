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

class LikeCondition implements ConditionInterface
{
    private $fieldName;
    private $pattern;
    private $value;
    private $allowedPatterns = ['left', 'right', 'both'];

    public function __construct(string $fieldName, string $pattern, string $value)
    {
        $this->fieldName = $fieldName;
        $this->pattern = $pattern;
        $this->value = $value;

        if (!in_array($pattern, $this->allowedPatterns, true)) {
            throw new \InvalidArgumentException(sprintf('Pattern %s not allowed, allowed are %s', $pattern, implode(', ', $this->allowedPatterns)));
        }
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function setFieldName(string $fieldName): void
    {
        $this->fieldName = $fieldName;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
