<?php

namespace CoreShop\Component\Index\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface IndexColumnInterface extends ResourceInterface
{
    /**
     * Field Type Integer for Index
     */
    const FIELD_TYPE_INTEGER = "INTEGER";

    /**
     * Field Type Double for Index
     */
    const FIELD_TYPE_DOUBLE = "DOUBLE";

    /**
     * Field Type String for Index
     */
    const FIELD_TYPE_STRING = "STRING";

    /**
     * Field Type Text for Index
     */
    const FIELD_TYPE_TEXT = "TEXT";

    /**
     * Field Type Boolean for Index
     */
    const FIELD_TYPE_BOOLEAN = "BOOLEAN";

    /**
     * Field Type Date for Index
     */
    const FIELD_TYPE_DATE = "DATE";

    /**
     * @return IndexInterface
     */
    public function getIndex();

    /**
     * @param IndexInterface $index
     * @return static
     */
    public function setIndex(IndexInterface $index);

    /**
     * @return string
     */
    public function getKey();

    /**
     * @param string $key
     */
    public function setKey($key);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getObjectType();

    /**
     * @param string $objectType
     */
    public function setObjectType($objectType);

    /**
     * @return string
     */
    public function getGetter();

    /**
     * @param string $getter
     */
    public function setGetter($getter);

    /**
     * @return array
     */
    public function getGetterConfig();

    /**
     * @param array $getterConfig
     */
    public function setGetterConfig($getterConfig);

    /**
     * @return string
     */
    public function getDataType();

    /**
     * @param string $dataType
     */
    public function setDataType($dataType);

    /**
     * @return string
     */
    public function getInterpreter();

    /**
     * @param string $interpreter
     */
    public function setInterpreter($interpreter);

    /**
     * @return array
     */
    public function getInterpreterConfig();

    /**
     * @param array $interpreterConfig
     */
    public function setInterpreterConfig($interpreterConfig);

    /**
     * @return string
     */
    public function getColumnType();

    /**
     * @param string $columnType
     */
    public function setColumnType($columnType);

    /**
     * @return array
     */
    public function getConfiguration();

    /**
     * @param array $configuration
     * @return static
     */
    public function setConfiguration($configuration);
}