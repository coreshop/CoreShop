<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Index\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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

    public function setWorker($worker)
    {
        $this->worker = $worker;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setClass($class)
    {
        $this->class = $class;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

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

    public function addColumn(IndexColumnInterface $column)
    {
        if (!$this->hasColumn($column)) {
            $this->columns->add($column);
            $column->setIndex($this);
        }
    }

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

    public function setIndexLastVersion($indexLastVersion)
    {
        $this->indexLastVersion = $indexLastVersion;
    }
}
