<?php

namespace CoreShop\Component\Index\Condition;

interface ConditionInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return mixed
     */
    public function getValues();

    /**
     * @param mixed $values
     */
    public function setValues($values);

    /**
     * @return string
     */
    public function getFieldName();

    /**
     * @param string $fieldName
     */
    public function setFieldName($fieldName);
}
