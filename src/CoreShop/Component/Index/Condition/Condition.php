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

/**
 * @deprecated this class is not supported anymore and will be removed in 2.0. Please use concrete Condition Classes instead.
 */
class Condition implements ConditionInterface
{
    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var mixed
     */
    protected $values = null;

    /**
     * Condition constructor.
     *
     * @param string $fieldName
     * @param string $type
     * @param mixed  $values
     */
    public function __construct($fieldName, $type, $values)
    {
        @trigger_error('Do not use Condition class directly anymore, the class has been deprecated and will be removed with 2.0. Please use a concrete Condition class instead.', E_USER_DEPRECATED);

        $this->fieldName = $fieldName;
        $this->type = $type;
        $this->values = $values;
    }

    /**
     * @deprecated don't use anymore, use IsCondition directly
     *
     * IN Condition (in)
     *
     * @param $fieldName
     * @param $null
     *
     * @return ConditionInterface
     */
    public static function is($fieldName, $null)
    {
        @trigger_error('is() is deprecated since version 2.0.0-beta.2 and will be removed in 2.0. Use IsCondition class instead.', E_USER_DEPRECATED);

        return new IsCondition($fieldName, $null);
    }

    /**
     * @deprecated don't use anymore, use InCondition directly
     *
     * IN Condition (in)
     *
     * @param $fieldName
     * @param $array
     *
     * @return ConditionInterface
     */
    public static function in($fieldName, $array)
    {
        @trigger_error('in() is deprecated since version 2.0.0-beta.2 and will be removed in 2.0. Use InCondition class instead.', E_USER_DEPRECATED);

        return new InCondition($fieldName, $array);
    }

    /**
     * @deprecated don't use anymore, use RangeCondition directly
     *
     * Range Condition (>=, <=)
     *
     * @param $fieldName
     * @param $fromRange
     * @param $toRange
     *
     * @return ConditionInterface
     */
    public static function range($fieldName, $fromRange, $toRange)
    {
        @trigger_error('range() is deprecated since version 2.0.0-beta.2 and will be removed in 2.0. Use RangeCondition class instead.', E_USER_DEPRECATED);

        return new RangeCondition($fieldName, $fromRange, $toRange);
    }

    /**
     * @deprecated don't use anymore, use ConcatCondition directly
     *
     * Concat Conditions with "AND" or "OR"
     *
     * @param $fieldName
     * @param ConditionInterface[] $conditions
     * @param string               $operator   ("AND", "OR")
     *
     * @return ConditionInterface
     */
    public static function concat($fieldName, $conditions, $operator)
    {
        @trigger_error('concat() is deprecated since version 2.0.0-beta.2 and will be removed in 2.0. Use ConcatCondition class instead.', E_USER_DEPRECATED);

        return new ConcatCondition($fieldName, $operator, $conditions);
    }

    /**
     * @deprecated don't use anymore, use LikeCondition directly
     *
     * Like Condition (%)
     *
     * @param $fieldName
     * @param $value
     * @param $patternPosition ("left", "right", "both")
     *
     * @return ConditionInterface
     */
    public static function like($fieldName, $value, $patternPosition)
    {
        @trigger_error('like() is deprecated since version 2.0.0-beta.2 and will be removed in 2.0. Use LikeCondition class instead.', E_USER_DEPRECATED);

        return new LikeCondition($fieldName, $patternPosition, $value);
    }

    /**
     * @deprecated don't use anymore, use MatchCondition directly
     *
     * Match Condition (=)
     *
     * @param $fieldName
     * @param $value
     *
     * @return ConditionInterface
     */
    public static function match($fieldName, $value)
    {
        @trigger_error('match() is deprecated since version 2.0.0-beta.2 and will be removed in 2.0. Use MatchCondition class instead.', E_USER_DEPRECATED);

        return new MatchCondition($fieldName, $value);
    }

    /**
     * @deprecated don't use anymore, use NotMatchCondition directly
     *
     * Match Condition (=)
     *
     * @param $fieldName
     * @param $value
     *
     * @return ConditionInterface
     */
    public static function notMatch($fieldName, $value)
    {
        @trigger_error('notMatch() is deprecated since version 2.0.0-beta.2 and will be removed in 2.0. Use NotMatchCondition class instead.', E_USER_DEPRECATED);

        return new NotMatchCondition($fieldName, $value);
    }

    /**
     * @deprecated don't use anymore, use LowerThanCondition directly
     *
     * Lower Than Condition (<)
     *
     * @param $fieldName
     * @param $value
     *
     * @return ConditionInterface
     */
    public static function lt($fieldName, $value)
    {
        @trigger_error('lt() is deprecated since version 2.0.0-beta.2 and will be removed in 2.0. Use LowerThanCondition class instead.', E_USER_DEPRECATED);

        return new LowerThanCondition($fieldName, $value);
    }

    /**
     * @deprecated don't use anymore, use LowerThanEqualCondition directly
     *
     * Lower Than Equal Condition (<=)
     *
     * @param $fieldName
     * @param $value
     *
     * @return ConditionInterface
     */
    public static function lte($fieldName, $value)
    {
        @trigger_error('lte() is deprecated since version 2.0.0-beta.2 and will be removed in 2.0. Use LowerThanEqualCondition class instead.', E_USER_DEPRECATED);

        return new LowerThanEqualCondition($fieldName, $value);
    }

    /**
     * @deprecated don't use anymore, use GreaterThanCondition directly
     *
     * Greater Than Condition (>)
     *
     * @param $fieldName
     * @param $value
     *
     * @return ConditionInterface
     */
    public static function gt($fieldName, $value)
    {
        @trigger_error('gt() is deprecated since version 2.0.0-beta.2 and will be removed in 2.0. Use GreaterThanCondition class instead.', E_USER_DEPRECATED);

        return new GreaterThanCondition($fieldName, $value);
    }

    /**
     * @deprecated don't use anymore, use GreaterThanEqualCondition directly
     *
     * Greater Than Equal Condition (<=)
     *
     * @param $fieldName
     * @param $value
     *
     * @return ConditionInterface
     */
    public static function gte($fieldName, $value)
    {
        @trigger_error('gte() is deprecated since version 2.0.0-beta.2 and will be removed in 2.0. Use GreaterThanEqualCondition class instead.', E_USER_DEPRECATED);

        return new GreaterThanEqualCondition($fieldName, $value);
    }

    /**
     * @deprecated don't use anymore, use CompareCondition directly
     *
     * Compare Condition ($operator)
     *
     * @param $fieldName
     * @param $value
     * @param $operator
     *
     * @return ConditionInterface
     */
    public static function compare($fieldName, $value, $operator)
    {
        @trigger_error('compare() is deprecated since version 2.0.0-beta.2 and will be removed in 2.0. Use CompareCondition class instead.', E_USER_DEPRECATED);

        return new CompareCondition($fieldName, $operator, $value);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param mixed $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param string $fieldName
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }
}
