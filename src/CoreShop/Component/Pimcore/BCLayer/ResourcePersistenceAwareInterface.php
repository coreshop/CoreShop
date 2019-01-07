<?php

namespace CoreShop\Component\Pimcore\BCLayer;

if (interface_exists(\Pimcore\Model\DataObject\ClassDefinition\Data\ResourcePersistenceAwareInterface::class)) {
    interface ResourcePersistenceAwareInterface extends \Pimcore\Model\DataObject\ClassDefinition\Data\ResourcePersistenceAwareInterface {

    }
}

else {
    interface ResourcePersistenceAwareInterface {

    }
}