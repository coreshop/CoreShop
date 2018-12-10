<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

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

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectKey()
    {
        return $this->objectKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setObjectKey($objectKey)
    {
        $this->objectKey = $objectKey;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * {@inheritdoc}
     */
    public function setObjectType($objectType)
    {
        $this->objectType = $objectType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasGetter()
    {
        return !empty($this->getter);
    }

    /**
     * {@inheritdoc}
     */
    public function getGetter()
    {
        return $this->getter;
    }

    /**
     * {@inheritdoc}
     */
    public function setGetter($getter)
    {
        $this->getter = $getter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGetterConfig()
    {
        return $this->getterConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function setGetterConfig($getterConfig)
    {
        $this->getterConfig = $getterConfig;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * {@inheritdoc}
     */
    public function setDataType($dataType)
    {
        $this->dataType = $dataType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasInterpreter()
    {
        return !empty($this->interpreter);
    }

    /**
     * {@inheritdoc}
     */
    public function getInterpreter()
    {
        return $this->interpreter;
    }

    /**
     * {@inheritdoc}
     */
    public function setInterpreter($interpreter)
    {
        $this->interpreter = $interpreter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInterpreterConfig()
    {
        return $this->interpreterConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function setInterpreterConfig($interpreterConfig)
    {
        $this->interpreterConfig = $interpreterConfig;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnType()
    {
        return $this->columnType;
    }

    /**
     * {@inheritdoc}
     */
    public function setColumnType($columnType)
    {
        $this->columnType = $columnType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function setIndex(IndexInterface $index = null)
    {
        $this->index = $index;
    }
}
