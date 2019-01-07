<?php

namespace CoreShop\Component\Pimcore\BCLayer;

if (class_exists(\Pimcore\Model\DataObject\ClassDefinition\Data\Href::class)) {
    class Href extends \Pimcore\Model\DataObject\ClassDefinition\Data\Href {

    }
}
elseif (class_exists(\Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation::class)) {
    class Href extends \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation {

    }
}
else {
    abstract class Href extends \Pimcore\Model\DataObject\ClassDefinition\Data {

    }

    throw new \RuntimeException(sprintf('This Exception should never be called, if it does get called, the class %s or %s is missing.', '\Pimcore\Model\DataObject\ClassDefinition\Data\Href', 'Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation'));
}