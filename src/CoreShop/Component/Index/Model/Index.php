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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @psalm-suppress MissingConstructor
 */
class Index extends AbstractResource implements IndexInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $class = '';

    /**
     * @var string
     */
    protected $worker = '';

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @var Collection|IndexColumnInterface[]
     */
    protected $columns;

    /**
     * @var bool
     */
    protected $indexLastVersion = false;

    public function __construct()
    {
        $this->columns = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * @return void
     */
    public function setWorker($worker)
    {
        $this->worker = $worker;
    }

    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return void
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return void
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function hasColumns()
    {
        return !$this->columns->isEmpty();
    }

    /**
     * @return void
     */
    public function addColumn(IndexColumnInterface $column)
    {
        if (!$this->hasColumn($column)) {
            $this->columns->add($column);
            $column->setIndex($this);
        }
    }

    /**
     * @return void
     */
    public function removeColumn(IndexColumnInterface $column)
    {
        if ($this->hasColumn($column)) {
            $this->columns->removeElement($column);
            $column->setIndex(null);
        }
    }

    public function hasColumn(IndexColumnInterface $column)
    {
        return $this->columns->contains($column);
    }

    public function getIndexLastVersion()
    {
        return $this->indexLastVersion;
    }

    /**
     * @return void
     */
    public function setIndexLastVersion($indexLastVersion)
    {
        $this->indexLastVersion = $indexLastVersion;
    }
}
