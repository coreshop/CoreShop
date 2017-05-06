<?php

namespace CoreShop\Component\Index\Getter;

use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

class BrickGetter implements GetterInterface
{
    /**
     * {@inheritdoc}
     */
    public function get(PimcoreModelInterface $object, IndexColumnInterface $config)
    {
        $columnConfig = $config->getConfiguration();
        $getterConfig = $config->getGetterConfig();

        if (isset($getterConfig['brickField']) && isset($columnConfig['className']) && isset($columnConfig['key'])) {
            $brickField = $getterConfig['brickField'];

            $brickContainerGetter = 'get'.ucfirst($brickField);
            $brickContainer = $object->$brickContainerGetter();
            $brickGetter = 'get'.ucfirst($columnConfig['className']);

            if ($brickContainer) {
                $brick = $brickContainer->$brickGetter();

                if ($brick) {
                    $fieldGetter = 'get'.ucfirst($columnConfig['key']);

                    return $brick->$fieldGetter();
                }
            }
        }

        return null;
    }
}
