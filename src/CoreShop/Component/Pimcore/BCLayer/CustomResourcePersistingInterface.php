<?php

namespace CoreShop\Component\Pimcore\BCLayer;

if (interface_exists(\Pimcore\Model\DataObject\ClassDefinition\Data\CustomResourcePersistingInterface::class)) {
    interface CustomResourcePersistingInterface extends \Pimcore\Model\DataObject\ClassDefinition\Data\CustomResourcePersistingInterface
    {
    }
} else {
    interface CustomResourcePersistingInterface
    {
    }
}
