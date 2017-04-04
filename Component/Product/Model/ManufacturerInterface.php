<?php

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface ManufacturerInterface extends PimcoreModelInterface
{
    /**
     * @param null $language
     * @return mixed
     */
    public function getName($language = null);

    /**
     * @param $name
     * @param null $language
     * @return mixed
     */
    public function setName($name, $language = null);
}