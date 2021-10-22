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

use Pimcore\Model\DataObject;
use Pimcore\Model\Exception\NotFoundException;

class ClassInstaller implements ClassInstallerInterface
{
    public function createBrick(string $jsonFile, string $brickName): DataObject\Objectbrick\Definition
    {
        try {
            $objectBrick = DataObject\Objectbrick\Definition::getByKey($brickName);
        } catch (NotFoundException $e) {
            $objectBrick = null;
        }

        if (null === $objectBrick) {
            $objectBrick = new DataObject\Objectbrick\Definition();
            $objectBrick->setKey($brickName);
        }

        $json = file_get_contents($jsonFile);

        DataObject\ClassDefinition\Service::importObjectBrickFromJson($objectBrick, $json, true);

        return $objectBrick;
    }

    public function createClass(string $jsonFile, string $className, bool $updateClass = false): DataObject\ClassDefinition
    {
        $tempClass = new DataObject\ClassDefinition();
        $class = null;

        try {
            /** @psalm-suppress InternalMethod */
            $id = $tempClass->getDao()->getIdByName($className);

            if ($id) {
                $class = DataObject\ClassDefinition::getById($id);
            }
        } catch (NotFoundException $exception) {
            //Ignore
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

        return $class;
    }

    public function createFieldCollection(string $jsonFile, string $name): DataObject\Fieldcollection\Definition
    {
        try {
            $fieldCollection = DataObject\Fieldcollection\Definition::getByKey($name);
        } catch (NotFoundException $e) {
            $fieldCollection = null;
        }

        if (null === $fieldCollection) {
            $fieldCollection = new DataObject\Fieldcollection\Definition();
            $fieldCollection->setKey($name);
        }

        $json = file_get_contents($jsonFile);

        DataObject\ClassDefinition\Service::importFieldCollectionFromJson($fieldCollection, $json, true);

        return $fieldCollection;
    }
}
