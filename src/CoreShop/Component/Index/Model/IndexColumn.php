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

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;

/**
 * @psalm-suppress MissingConstructor
 */
class IndexColumn extends AbstractResource implements IndexColumnInterface, \Stringable
{
    use TimestampableTrait;

    public ?int $id = null;

    public ?string $type = null;

    public ?string $objectKey = null;

    public ?string $name = null;

    public ?string $objectType = null;

    public ?string $getter = null;

    public array $getterConfig = [];

    public ?string $dataType = null;

    public ?string $interpreter = null;

    public array $interpreterConfig = [];

    public ?string $columnType = null;

    public array $configuration = [];

    public ?IndexInterface $index;

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->getName(), $this->getId());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjectKey()
    {
        return $this->objectKey;
    }

    public function setObjectKey($key)
    {
        $this->objectKey = $key;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getObjectType()
    {
        return $this->objectType;
    }

    public function setObjectType($objectType)
    {
        $this->objectType = $objectType;

        return $this;
    }

    public function hasGetter()
    {
        return !empty($this->getter);
    }

    public function getGetter()
    {
        return $this->getter;
    }

    public function setGetter($getter)
    {
        $this->getter = $getter;

        return $this;
    }

    public function getGetterConfig()
    {
        return $this->getterConfig;
    }

    public function setGetterConfig($getterConfig)
    {
        $this->getterConfig = $getterConfig;

        return $this;
    }

    public function getDataType()
    {
        return $this->dataType;
    }

    public function setDataType($dataType)
    {
        $this->dataType = $dataType;

        return $this;
    }

    public function hasInterpreter()
    {
        return !empty($this->interpreter);
    }

    public function getInterpreter()
    {
        return $this->interpreter;
    }

    public function setInterpreter($interpreter)
    {
        $this->interpreter = $interpreter;

        return $this;
    }

    public function getInterpreterConfig()
    {
        return $this->interpreterConfig;
    }

    public function setInterpreterConfig($interpreterConfig)
    {
        $this->interpreterConfig = $interpreterConfig;

        return $this;
    }

    public function getColumnType()
    {
        return $this->columnType;
    }

    public function setColumnType($columnType)
    {
        $this->columnType = $columnType;

        return $this;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function setIndex(?IndexInterface $index)
    {
        $this->index = $index;
    }
}
