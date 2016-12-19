<?php

namespace CoreShop\IndexService;

/**
 * Class Condition
 * @package CoreShop\IndexService
 */
class Condition
{

    /**
     * @var string
     */
    protected $type = "";

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
     * @param string $type
     * @param mixed $values
     */
    public function __construct($fieldName, $type, $values)
    {
        $this->fieldName = $fieldName;
        $this->type = $type;
        $this->values = $values;
    }

    /**
     * IN Condition (in)
     *
     * @param $fieldName
     * @param $array
     * @return Condition
     */
    public static function in($fieldName, $array)
    {
        return new Condition($fieldName, "in", $array);
    }

    /**
     * Range Condition (>=, <=)
     *
     * @param $fieldName
     * @param $from
     * @param $to
     *
     * @return Condition
     */
    public static function range($fieldName, $from, $to)
    {
        return new Condition($fieldName, "range", ["from" => $from, "to" => $to]);
    }

    /**
     * Concat Conditions with "AND" or "OR"
     *
     * @param $fieldName
     * @param Condition[] $conditions
     * @param string $operator ("AND", "OR")
     * @return Condition
     */
    public static function concat($fieldName, $conditions, $operator)
    {
        return new Condition($fieldName, "concat", ["operator" => $operator, "conditions" => $conditions]);
    }

    /**
     * Like Condition (%)
     *
     * @param $fieldName
     * @param $value
     * @param $patternPosition ("left", "right", "both")
     * @return Condition
     */
    public static function like($fieldName, $value, $patternPosition)
    {
        return new Condition($fieldName, "like", ["value" => $value, "pattern" => $patternPosition]);
    }

    /**
     * Match Condition (=)
     *
     * @param $fieldName
     * @param $value
     * @return Condition
     */
    public static function match($fieldName, $value)
    {
        return static::compare($fieldName, $value, '=');
    }

    /**
     * Match Condition (=)
     *
     * @param $fieldName
     * @param $value
     * @return Condition
     */
    public static function notMatch($fieldName, $value)
    {
        return static::compare($fieldName, $value, '!=');
    }

    /**
     * Lower Than Condition (<)
     *
     * @param $fieldName
     * @param $value
     * @return Condition
     */
    public static function lt($fieldName, $value)
    {
        return static::compare($fieldName, $value, '<');
    }

    /**
     * Lower Than Equal Condition (<=)
     *
     * @param $fieldName
     * @param $value
     * @return Condition
     */
    public static function lte($fieldName, $value)
    {
        return static::compare($fieldName, $value, '<=');
    }

    /**
     * Greater Than Condition (>)
     *
     * @param $fieldName
     * @param $value
     * @return Condition
     */
    public static function gt($fieldName, $value)
    {
        return static::compare($fieldName, $value, '>');
    }

    /**
     * Greater Than Equal Condition (<=)
     *
     * @param $fieldName
     * @param $value
     * @return Condition
     */
    public static function gte($fieldName, $value)
    {
        return static::compare($fieldName, $value, '>=');
    }

    /**
     * Compare Condition ($operator)
     *
     * @param $fieldName
     * @param $value
     * @param $operator
     * @return Condition
     */
    public static function compare($fieldName, $value, $operator)
    {
        return new Condition($fieldName, "compare", ["value" => $value, "operator" => $operator]);
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
