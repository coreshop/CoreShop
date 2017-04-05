<?php

namespace CoreShop\Component\Index\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Index extends AbstractResource implements IndexInterface
{
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
    protected $class = "";

    /**
     * @var string
     */
    protected $worker = "";

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @var Collection|IndexColumnInterface[]
     */
    protected $columns;

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
}