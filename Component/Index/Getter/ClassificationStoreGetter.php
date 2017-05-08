<?php

namespace CoreShop\Component\Index\Getter;

use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

class ClassificationStoreGetter implements GetterInterface
{
    /**
     * {@inheritdoc}
     */
    public function get(PimcoreModelInterface $object, IndexColumnInterface $config)
    {
        $columnConfig = $config->getConfiguration();

        $classificationStore = $config->getGetterConfig()['classificationStoreField'];
        $classificationStoreGetter = 'get' . ucfirst($classificationStore);

        if (method_exists($object, $classificationStoreGetter)) {
            $classificationStore = $object->$classificationStoreGetter();

            if ($classificationStore instanceof \Pimcore\Model\Object\Classificationstore) {
                return $classificationStore->getLocalizedKeyValue($columnConfig['groupConfigId'], $columnConfig['keyConfigId']);
            }
        }

        return null;
    }
}
