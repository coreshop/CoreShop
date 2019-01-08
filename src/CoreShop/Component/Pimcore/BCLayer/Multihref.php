<?php

namespace CoreShop\Component\Pimcore\BCLayer;

if (class_exists(\Pimcore\Model\DataObject\ClassDefinition\Data\Multihref::class)) {
    class Multihref extends \Pimcore\Model\DataObject\ClassDefinition\Data\Multihref
    {
    }
} elseif (class_exists(\Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyRelation::class)) {
    class Multihref extends \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyRelation
    {
    }
} else {
    abstract class Multihref extends \Pimcore\Model\DataObject\ClassDefinition\Data
    {
    }

    throw new \RuntimeException(sprintf('This Exception should never be called, if it does get called, the class %s or %s is missing.', '\Pimcore\Model\DataObject\ClassDefinition\Data\Multihref', 'Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyRelation'));
}
