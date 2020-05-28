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
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * {@inheritdoc}
     */
    public function setWorker($worker)
    {
        $this->worker = $worker;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function setClass($class)
    {
        $this->class = $class;
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
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumns()
    {
        return !$this->columns->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addColumn(IndexColumnInterface $column)
    {
        if (!$this->hasColumn($column)) {
            $this->columns->add($column);
            $column->setIndex($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeColumn(IndexColumnInterface $column)
    {
        if ($this->hasColumn($column)) {
            $this->columns->removeElement($column);
            $column->setIndex(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumn(IndexColumnInterface $column)
    {
        return $this->columns->contains($column);
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexLastVersion()
    {
        return $this->indexLastVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function setIndexLastVersion($indexLastVersion)
    {
        $this->indexLastVersion = $indexLastVersion;
    }
}
