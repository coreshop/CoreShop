<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Index\Getter;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use Pimcore\Model\DataObject\Classificationstore;

class ClassificationStoreGetter implements GetterInterface
{
    public function get(IndexableInterface $object, IndexColumnInterface $config): mixed
    {
        $columnConfig = $config->getConfiguration();

        $classificationStore = $config->getGetterConfig()['classificationStoreField'];
        $classificationStoreGetter = 'get' . ucfirst($classificationStore);

        if (method_exists($object, $classificationStoreGetter)) {
            $classificationStore = $object->$classificationStoreGetter();

            if ($classificationStore instanceof Classificationstore) {
                return $classificationStore->getLocalizedKeyValue($columnConfig['groupConfigId'], $columnConfig['keyConfigId']);
            }
        }

        return null;
    }
}
