<?php

namespace CoreShop\Component\Index\Getter;

use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

class FieldCollectionGetter implements GetterInterface
{
    /**
     * {@inheritdoc}
     */
    public function get(PimcoreModelInterface $object, IndexColumnInterface $config)
    {
        $columnConfig = $config->getConfiguration();
        $fieldValues = [];
        $collectionField = $config->getGetterConfig()['collectionField'];

        $collectionContainerGetter = 'get' . ucfirst($collectionField);
        $collectionContainer = $object->$collectionContainerGetter();
        $validItems = [];
        $fieldGetter = 'get' . ucfirst($columnConfig['key']);

        if ($collectionContainer instanceof \Pimcore\Model\Object\Fieldcollection) {
            foreach ($collectionContainer->getItems() as $item) {
                $className = 'Pimcore\Model\Object\Fieldcollection\Data\\' . $columnConfig['className'];
                if (is_a($item, $className)) {
                    $validItems[] = $item;
                }
            }
        }

        foreach ($validItems as $item) {
            if (method_exists($item, $fieldGetter)) {
                $fieldValues[] = $item->$fieldGetter();
            }
        }

        return count($fieldValues) > 0 ? $fieldValues : null;
    }
}
