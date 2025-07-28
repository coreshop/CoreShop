<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Index\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;

interface IndexColumnInterface extends ResourceInterface, TimestampableInterface
{
    public function getId(): ?int;

    /**
     * @return IndexInterface|null
     */
    public function getIndex();

    public function setIndex(?IndexInterface $index);

    /**
     * @return string
     */
    public function getObjectKey();

    /**
     * @param string $key
     */
    public function setObjectKey($key);

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
     * @return bool
     */
    public function hasGetter();

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
     * @return bool
     */
    public function hasInterpreter();

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
     */
    public function setConfiguration($configuration);
}
