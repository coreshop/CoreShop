<?php

namespace CoreShop\Component\Core\Pimcore;

use Pimcore\Model\Object\Service;

class ObjectService implements ObjectServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFolderByPath($path)
    {
        return Service::createFolderByPath($path);
    }
}