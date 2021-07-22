<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject;

class ClassInstaller implements ClassInstallerInterface
{
    /**
     * {@inheritdoc}
     */
    public function createBrick($jsonFile, $brickName)
    {
        try {
            $objectBrick = DataObject\Objectbrick\Definition::getByKey($brickName);
        } catch (\Exception $e) {
            $objectBrick = null;
        }

        if (null === $objectBrick) {
            $objectBrick = new DataObject\Objectbrick\Definition();
            $objectBrick->setKey($brickName);
        }

        $json = file_get_contents($jsonFile);

        DataObject\ClassDefinition\Service::importObjectBrickFromJson($objectBrick, $json, true);

        ClassLoader::forceLoadBrick($brickName);

        return $objectBrick;
    }

    /**
     * {@inheritdoc}
     */
    public function createClass($jsonFile, $className, $updateClass = false)
    {
        $tempClass = new DataObject\ClassDefinition();
        $id = $tempClass->getDao()->getIdByName($className);
        $class = null;

        if ($id) {
            $class = DataObject\ClassDefinition::getById($id);
        }

        if (!$class || $updateClass) {
            $json = file_get_contents($jsonFile);

            if (!$class) {
                $class = DataObject\ClassDefinition::create();
            }

            $class->setName($className);
            $class->setUserOwner(0);

            DataObject\ClassDefinition\Service::importClassDefinitionFromJson($class, $json, true);

            /**
             * Fixes Object Brick Stuff.
             */
            $list = new DataObject\Objectbrick\Definition\Listing();
            $list = $list->load();

            if (!empty($list)) {
                foreach ($list as $brickDefinition) {
                    $clsDefs = $brickDefinition->getClassDefinitions();
                    if (!empty($clsDefs)) {
                        foreach ($clsDefs as $cd) {
                            if ($cd['classname'] == $class->getId()) {
                                $brickDefinition->save();
                            }
                        }
                    }
                }
            }
        }

        ClassLoader::forceLoadDataObjectClass($className);

        return $class;
    }

    /**
     * {@inheritdoc}
     */
    public function createFieldCollection($jsonFile, $name)
    {
        try {
            $fieldCollection = DataObject\Fieldcollection\Definition::getByKey($name);
        } catch (\Exception $e) {
            $fieldCollection = null;
        }

        if (null === $fieldCollection) {
            $fieldCollection = new DataObject\Fieldcollection\Definition();
            $fieldCollection->setKey($name);
        }

        $json = file_get_contents($jsonFile);

        DataObject\ClassDefinition\Service::importFieldCollectionFromJson($fieldCollection, $json, true);

        ClassLoader::forceLoadFieldCollection($name);

        return $fieldCollection;
    }
}
