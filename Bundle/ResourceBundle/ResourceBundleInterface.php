<?php

namespace CoreShop\Bundle\ResourceBundle;

interface ResourceBundleInterface
{
    const MAPPING_XML = 'xml';
    const MAPPING_YAML = 'yaml';
    const MAPPING_ANNOTATION = 'annotation';

    /**
     * Returns a vector of supported drivers.
     *
     * @see CoreShopCoreBundle::DRIVER_DOCTRINE_ORM
     * @see CoreShopCoreBundle::DRIVER_DOCTRINE_MONGODB_ODM
     * @see CoreShopCoreBundle::DRIVER_DOCTRINE_PHPCR_ODM
     *
     * @return array
     */
    public function getSupportedDrivers();
}
