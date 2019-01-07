<?php

namespace CoreShop\Component\Pimcore\BCLayer;

if (interface_exists(\Pimcore\Model\DataObject\ClassDefinition\Data\QueryResourcePersistenceAwareInterface::class)) {
    interface QueryResourcePersistenceAwareInterface extends \Pimcore\Model\DataObject\ClassDefinition\Data\QueryResourcePersistenceAwareInterface {

    }
}

else {
    interface QueryResourcePersistenceAwareInterface {

    }
}