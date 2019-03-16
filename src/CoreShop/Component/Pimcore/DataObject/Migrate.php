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

use CoreShop\Component\Pimcore\Db\Db;
use CoreShop\Component\Pimcore\Exception\ClassDefinitionAlreadyExistsException;
use CoreShop\Component\Pimcore\Exception\ClassDefinitionNotFoundException;
use Pimcore\Cache;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Tool;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;

final class Migrate
{
    /**
     * @param string $fromClass
     * @param string $toClass
     * @param array  $options
     *
     * @return ClassDefinition
     *
     * @throws ClassDefinitionAlreadyExistsException
     * @throws ClassDefinitionNotFoundException
     */
    public static function migrateClass($fromClass, $toClass, $options = [])
    {
        $newClassDefinition = ClassDefinition::getByName($toClass);

        if ($newClassDefinition instanceof ClassDefinition) {
            if (array_key_exists('delete_existing_class', $options) && $options['delete_existing_class']) {
                $newClassDefinition->delete();
            } else {
                throw new ClassDefinitionAlreadyExistsException();
            }
        }

        $classDefinition = ClassDefinition::getByName($fromClass);

        //Somehow ::generateClassDefinitionJson destroys the field-definitions, this line repairs it. So we just remove it from \Zend_Registry
        Cache\Runtime::getInstance()->offsetUnset('class_' . $classDefinition->getId());

        if (!$classDefinition instanceof ClassDefinition) {
            throw new ClassDefinitionNotFoundException();
        }

        $jsonDefinition = ClassDefinition\Service::generateClassDefinitionJson($classDefinition);

        if (array_key_exists('layoutDefinitions', $options)) {
            throw new OptionDefinitionException('Option \'layoutDefinitions\' not allowed');
        }

        $json = json_decode($jsonDefinition, true);

        foreach ($options as $key => $value) {
            $json[$key] = $value;
        }

        $json = json_encode($json);

        $class = ClassDefinition::create();
        $class->setName($toClass);
        $class->setUserOwner(0); //0 = SystemId

        ClassDefinition\Service::importClassDefinitionFromJson($class, $json, true);

        $list = new Objectbrick\Definition\Listing();
        $list = $list->load();

        if (is_array($list)) {
            foreach ($list as $brickDefinition) {
                if ($brickDefinition instanceof Objectbrick\Definition) {
                    $clsDef = $brickDefinition->getClassDefinitions();

                    if (is_array($clsDef)) {
                        $fieldName = null;

                        foreach ($clsDef as $cd) {
                            if ($cd['classname'] == $classDefinition->getId()) {
                                $fieldName = $cd['fieldname'];

                                break;
                            }
                        }

                        if ($fieldName) {
                            $clsDef[] = [
                                'classname' => $class->getId(),
                                'fieldname' => $fieldName,
                            ];

                            $brickDefinition->setClassDefinitions($clsDef);
                            $brickDefinition->save();
                        }
                    }
                }
            }
        }

        foreach ($class->getFieldDefinitions() as $fd) {
            if ($fd instanceof ClassDefinition\Data\Fieldcollections) {
                foreach ($fd->getAllowedTypes() as $type) {
                    $definition = Fieldcollection\Definition::getByKey($type);

                    if (method_exists('createUpdateTable', $definition)) {
                        $definition->createUpdateTable($class);
                    }
                }
            }
        }

        return $newClassDefinition;
    }

    /**
     * Migrates all the data from $oldClassDefinition to $newClassDefinition.
     *
     * @param string $oldPimcoreClass
     * @param string $newPimcoreClass
     *
     * @throws \Exception
     */
    public static function migrateData($oldPimcoreClass, $newPimcoreClass)
    {
        $oldClassDefinition = ClassDefinition::getByName($oldPimcoreClass);
        $newClassDefinition = ClassDefinition::getByName($newPimcoreClass);

        if (!$oldClassDefinition) {
            throw new ClassDefinitionNotFoundException("Could not find the ClassDefinition for class $oldPimcoreClass");
        }

        if (!$newClassDefinition) {
            throw new ClassDefinitionNotFoundException("Could not find the ClassDefinition for class $newPimcoreClass");
        }

        $oldClassId = $oldClassDefinition->getId();
        $newClassId = $newClassDefinition->getId();

        $db = Db::get();

        $tablesToMigrate = [
            'object_query_%s' => true,
            'object_store_%s' => false,
            'object_relations_%s' => false,
        ];

        foreach ($oldClassDefinition->getFieldDefinitions() as $fd) {
            if ($fd instanceof ClassDefinition\Data\Objectbricks) {
                foreach ($fd->getAllowedTypes() as $type) {
                    $definition = Objectbrick\Definition::getByKey($type);

                    $tablesToMigrate['object_brick_query_' . $definition->getKey() . '_%s'] = false;
                    $tablesToMigrate['object_brick_store_' . $definition->getKey() . '_%s'] = false;
                }
            } elseif ($fd instanceof ClassDefinition\Data\Fieldcollections) {
                foreach ($fd->getAllowedTypes() as $type) {
                    $definition = Fieldcollection\Definition::getByKey($type);

                    if ($definition instanceof Fieldcollection\Definition) {
                        $tablesToMigrate['object_collection_' . $definition->getKey() . '_%s'] = false;

                        foreach ($definition->getFieldDefinitions() as $fieldDef) {
                            if ($fieldDef instanceof ClassDefinition\Data\Localizedfields) {
                                $tablesToMigrate['object_collection_' . $definition->getKey() . '_localized_%s'] = false;
                            }
                        }
                    }
                }
            } elseif ($fd instanceof ClassDefinition\Data\Localizedfields) {
                $tablesToMigrate['object_localized_data_%s'] = false;

                $validLanguages = Tool::getValidLanguages();

                foreach ($validLanguages as $lang) {
                    $tablesToMigrate['object_localized_query_%s_' . $lang] = false;
                }
            } elseif ($fd instanceof ClassDefinition\Data\Classificationstore) {
                $tablesToMigrate['object_classificationstore_data_%s'] = false;
                $tablesToMigrate['object_classificationstore_groups_%s'] = false;
            }
        }

        foreach ($tablesToMigrate as $tbl => $replaceClassNames) {
            $oldSqlTable = sprintf($tbl, $oldClassId);
            $newSqlTable = sprintf($tbl, $newClassId);

            if (!Db::tableExists($oldSqlTable)) {
                continue;
            }

            $columns = Db::getColumns($newSqlTable);

            foreach ($columns as &$column) {
                $column = $db->quoteIdentifier($column);
            }

            $sql = "INSERT INTO $newSqlTable SELECT " . implode(',', $columns) . " FROM $oldSqlTable";

            $db->executeQuery($sql);

            if ($replaceClassNames) {
                $sql = "UPDATE $newSqlTable SET oo_classId=?, oo_className=?";

                $db->executeQuery($sql, [$newClassDefinition->getId(), $newClassDefinition->getName()]);
            }
        }

        $db->executeQuery('UPDATE objects SET o_classId=?, o_className=? WHERE o_classId=?', [$newClassDefinition->getId(), $newClassDefinition->getName(), $oldClassDefinition->getId()]);
    }
}
