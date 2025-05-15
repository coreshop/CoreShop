<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Pimcore\DataObject;

use CoreShop\Bundle\MoneyBundle\CoreExtension\Money;
use Pimcore\Model\DataObject;

class DataLoader implements DataLoaderInterface
{
    public function getDataForObject(DataObject\Concrete $data, array $loadedObjects = []): array
    {
        $objectData = [];
        DataObject\Service::loadAllObjectFields($data);

        $loadedObjects[] = $data->getId();

        foreach ($data->getClass()->getFieldDefinitions() as $key => $def) {
            $getter = 'get' . ucfirst($key);

            if (!method_exists($data, $getter)) {
                continue;
            }

            $fieldData = $data->$getter();

            if ($def instanceof DataObject\ClassDefinition\Data\ManyToOneRelation) {
                if ($fieldData instanceof DataObject\Concrete) {
                    if (!in_array($fieldData->getId(), $loadedObjects)) {
                        $objectData[$key] = $this->getDataForObject($fieldData, $loadedObjects);
                    }
                }
            } elseif ($def instanceof DataObject\ClassDefinition\Data\ManyToManyRelation) {
                $objectData[$key] = [];

                if (!is_array($fieldData)) {
                    continue;
                }

                foreach ($fieldData as $object) {
                    if ($object instanceof DataObject\Concrete) {
                        if (!in_array($object->getId(), $loadedObjects)) {
                            $objectData[$key][] = $this->getDataForObject($object, $loadedObjects);
                        }
                    }
                }
            } elseif ($def instanceof DataObject\ClassDefinition\Data) {
                if (class_exists(Money::class) && $def instanceof Money) {
                    $value = $fieldData;
                } else {
                    $value = $def->getDataForEditmode($fieldData, $data);
                }

                $objectData[$key] = $value;
            } else {
                $objectData[$key] = null;
            }
        }

        $loadedObjects[] = $data->getId();

        $objectData['id'] = $data->getId();
        $objectData['creationDate'] = $data->getCreationDate();
        $objectData['modificationDate'] = $data->getModificationDate();

        return $objectData;
    }
}
