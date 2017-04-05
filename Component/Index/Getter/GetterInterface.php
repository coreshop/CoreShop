<?php

namespace CoreShop\Component\Index\Getter;

use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface GetterInterface
{
    /**
     * @param PimcoreModelInterface $object
     * @param IndexColumnInterface $column
     * @return mixed
     */
    public function get(PimcoreModelInterface $object, IndexColumnInterface $column);
}