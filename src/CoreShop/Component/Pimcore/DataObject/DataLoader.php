<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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

        $objectData['o_id'] = $data->getId();
        $objectData['o_creationDate'] = $data->getCreationDate();
        $objectData['o_modificationDate'] = $data->getModificationDate();

        return $objectData;
    }
}
