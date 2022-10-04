<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Index\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;

/**
 * @psalm-suppress MissingConstructor
 */
class FilterCondition extends AbstractResource implements FilterConditionInterface
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
     * @var int
     */
    public $sort;

    /**
     * @var string
     */
    public $field;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $quantityUnit;

    /**
     * @var array
     */
    public $configuration;

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @return void
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return static
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function getQuantityUnit()
    {
        return $this->quantityUnit;
    }

    /**
     * @return static
     */
    public function setQuantityUnit($quantityUnit)
    {
        $this->quantityUnit = $quantityUnit;

        return $this;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return static
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }
}
