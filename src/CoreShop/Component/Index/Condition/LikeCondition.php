<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Index\Condition;

class LikeCondition implements ConditionInterface
{
    /**
     * @var $string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var string
     */
    private $value;

    private $allowedPatterns = ['left', 'right', 'both'];

    /**
     * @param $fieldName
     * @param string $pattern
     * @param string $value
     */
    public function __construct($fieldName, string $pattern, string $value)
    {
        $this->fieldName = $fieldName;
        $this->pattern = $pattern;
        $this->value = $value;

        if (!in_array($pattern, $this->allowedPatterns)) {
            throw new \InvalidArgumentException(sprintf('Pattern %s not allowed, allowed are %s', $pattern, implode(', ', $this->allowedPatterns)));
        }
    }

    /**
     * @return mixed
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param mixed $fieldName
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     */
    public function setPattern(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }
}
