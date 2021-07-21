<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Index\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;

class IndexColumn extends AbstractResource implements IndexColumnInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $objectKey;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $objectType;

    /**
     * @var string
     */
    public $getter;

    /**
     * @var array
     */
    public $getterConfig;

    /**
     * @var string
     */
    public $dataType;

    /**
     * @var string
     */
    public $interpreter;

    /**
     * @var array
     */
    public $interpreterConfig;

    /**
     * @var string
     */
    public $columnType;

    /**
     * @var array
     */
    public $configuration;

    /**
     * @var IndexInterface
     */
    public $index;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s (%s)', $this->getName(), $this->getId());
    }

    public function getId()
    {
        return $this->id;
    }

    public function getObjectKey()
    {
        return $this->objectKey;
    }

    public function setObjectKey($objectKey)
    {
        $this->objectKey = $objectKey;

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

    public function setIndex(IndexInterface $index = null)
    {
        $this->index = $index;
    }
}
