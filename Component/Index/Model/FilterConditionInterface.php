<?php

namespace CoreShop\Component\Index\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;

interface FilterConditionInterface extends ResourceInterface
{
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
    public function getField();

    /**
     * @param string $field
     * @return static
     */
    public function setField($field);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     * @return static
     */
    public function setLabel($label);

    /**
     * @return int
     */
    public function getQuantityUnit();

    /**
     * @param int $quantityUnit
     * @return static
     */
    public function setQuantityUnit($quantityUnit);

    /**
     * @return array
     */
    public function getConfiguration();

    /**
     * @param array $configuration
     * @return static
     */
    public function setConfiguration($configuration);

    /**
     * @return FilterInterface
     */
    public function getFilter();

    /**
     * @param FilterInterface $filter
     * @return static
     */
    public function setFilter(FilterInterface $filter);
}