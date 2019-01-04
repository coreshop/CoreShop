<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject;

class EditmodeHelper
{
    private $objectData = [];
    private $metaData = [];

    public function getDataForObject(DataObject\Concrete $object, $objectFromVersion = false)
    {
        $this->objectData = [];

        foreach ($object->getClass()->getFieldDefinitions(['object' => $object]) as $key => $def) {
            $this->getDataForEditmode($object, $key, $def, $objectFromVersion);
        }

        return [
            'objectData' => $this->objectData,
            'metaData' => $this->metaData,
        ];
    }

    private function getDataForEditmode($object, $key, $fielddefinition, $objectFromVersion, $level = 0)
    {
        $parent = DataObject\Service::hasInheritableParentObject($object);
        $getter = 'get' . ucfirst($key);

        // relations but not for objectsMetadata, because they have additional data which cannot be loaded directly from the DB
        // nonownerobjects should go in there anyway (regardless if it a version or not), so that the values can be loaded
        if ((
                !$objectFromVersion
                && $fielddefinition instanceof DataObject\ClassDefinition\Data\Relations\AbstractRelations
                && $fielddefinition->getLazyLoading()
                && !$fielddefinition instanceof DataObject\ClassDefinition\Data\ObjectsMetadata
                && !$fielddefinition instanceof DataObject\ClassDefinition\Data\MultihrefMetadata
            )
            || $fielddefinition instanceof DataObject\ClassDefinition\Data\Nonownerobjects
        ) {
            //lazy loading data is fetched from DB differently, so that not every relation object is instantiated
            $refId = null;

            if ($fielddefinition->isRemoteOwner() &&
                method_exists($fielddefinition, 'getOwnerFieldName') &&
                method_exists($fielddefinition, 'getOwnerClassName')) {
                $refKey = $fielddefinition->getOwnerFieldName();
                $refClass = DataObject\ClassDefinition::getByName($fielddefinition->getOwnerClassName());
                if ($refClass) {
                    $refId = $refClass->getId();
                }
            } else {
                $refKey = $key;
            }
            $relations = $object->getRelationData($refKey, !$fielddefinition->isRemoteOwner(), $refId);
            if (empty($relations) && !empty($parent)) {
                $this->getDataForEditmode($parent, $key, $fielddefinition, $objectFromVersion, $level + 1);
            } else {
                $data = [];

                if ($fielddefinition instanceof DataObject\ClassDefinition\Data\Href) {
                    $data = $relations[0];
                } else {
                    foreach ($relations as $rel) {
                        if ($fielddefinition instanceof DataObject\ClassDefinition\Data\Objects) {
                            $data[] = [$rel['id'], $rel['path'], $rel['subtype']];
                        } else {
                            $data[] = [$rel['id'], $rel['path'], $rel['type'], $rel['subtype']];
                        }
                    }
                }
                $this->objectData[$key] = $data;
                $this->metaData[$key]['objectid'] = $object->getId();
                $this->metaData[$key]['inherited'] = $level != 0;
            }
        } else {
            $fieldData = $object->$getter();
            $isInheritedValue = false;

            if ($fielddefinition instanceof DataObject\ClassDefinition\Data\CalculatedValue) {
                $fieldData = new DataObject\Data\CalculatedValue($fielddefinition->getName());
                $fieldData->setContextualData('object', null, null, null);
                $value = $fielddefinition->getDataForEditmode($fieldData, $object, $objectFromVersion);
            } else {
                $value = $fielddefinition->getDataForEditmode($fieldData, $object, $objectFromVersion);
            }

            // following some exceptions for special data types (localizedfields, objectbricks)
            if ($value && ($fieldData instanceof DataObject\Localizedfield || $fieldData instanceof DataObject\Classificationstore)) {
                // make sure that the localized field participates in the inheritance detection process
                $isInheritedValue = $value['inherited'];
            }
            if ($fielddefinition instanceof DataObject\ClassDefinition\Data\Objectbricks && is_array($value)) {
                // make sure that the objectbricks participate in the inheritance detection process
                foreach ($value as $singleBrickData) {
                    if ($singleBrickData['inherited']) {
                        $isInheritedValue = true;
                    }
                }
            }

            if ($fielddefinition->isEmpty($fieldData) && !empty($parent)) {
                $this->getDataForEditmode($parent, $key, $fielddefinition, $objectFromVersion, $level + 1);
            } else {
                $isInheritedValue = $isInheritedValue || ($level != 0);
                $this->metaData[$key]['objectid'] = $object->getId();

                $this->objectData[$key] = $value;
                $this->metaData[$key]['inherited'] = $isInheritedValue;

                if ($isInheritedValue && !$fielddefinition->isEmpty($fieldData) && !$this->isInheritableField($fielddefinition)) {
                    $this->objectData[$key] = null;
                    $this->metaData[$key]['inherited'] = false;
                    $this->metaData[$key]['hasParentValue'] = true;
                }
            }
        }
    }

    private function isInheritableField(DataObject\ClassDefinition\Data $fielddefinition)
    {
        if ($fielddefinition instanceof DataObject\ClassDefinition\Data\Fieldcollections
            //            || $fielddefinition instanceof DataObject\ClassDefinition\Data\Localizedfields
        ) {
            return false;
        }

        return true;
    }
}
