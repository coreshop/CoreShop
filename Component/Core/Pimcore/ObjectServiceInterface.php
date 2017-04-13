<?php

namespace CoreShop\Component\Core\Pimcore;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Pimcore\Model\Element\ElementInterface;

interface ObjectServiceInterface
{
    /**
     * @param $path
     *
     * @return ElementInterface
     */
    public function createFolderByPath($path);

    /**
     * Copy all fields from $from to $to
     *
     * @param PimcoreModelInterface $from
     * @param PimcoreModelInterface $to
     * @return mixed
     */
    public function copyObject(PimcoreModelInterface $from, PimcoreModelInterface $to);
}
