<?php

namespace CoreShop\Component\Core\Pimcore;

use Pimcore\Model\Element\ElementInterface;

interface ObjectServiceInterface
{
    /**
     * @param $path
     *
     * @return ElementInterface
     */
    public function createFolderByPath($path);
}
