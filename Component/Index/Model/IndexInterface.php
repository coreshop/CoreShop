<?php

namespace CoreShop\Component\Index\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;

interface IndexInterface extends ResourceInterface {
    /**
     * @return string
     */
    public function getName();

    /**
     * @param $name
     * @return static
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return static
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getClass();

    /**
     * @param string $class
     * @return static
     */
    public function setClass($class);

    /**
     * @return array
     */
    public function getConfig();

    /**
     * @param array $config
     * @return static
     */
    public function setConfig($config);

     /**
     * @return Collection|IndexColumnInterface[]
     */
    public function getColumns();

    /**
     * @return bool
     */
    public function hasColumns();

    /**
     * @param IndexColumnInterface $column
     */
    public function addColumn(IndexColumnInterface $column);

    /**
     * @param IndexColumnInterface $column
     */
    public function removeColumn(IndexColumnInterface $column);

    /**
     * @param IndexColumnInterface $column
     *
     * @return bool
     */
    public function hasColumn(IndexColumnInterface $column);
}