<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Pimcore\BrickDefinitionUpdate;
use CoreShop\Component\Pimcore\ClassUpdate;
use CoreShop\Component\Pimcore\ClassUpdateInterface;
use CoreShop\Component\Pimcore\FieldCollectionDefinitionUpdate;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Objectbrick;

final class PimcoreClassContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(SharedStorageInterface $sharedStorage)
    {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given /^there is a pimcore class "([^"]+)"$/
     */
    public function createClassNamed($className)
    {
        $classDefinition = new ClassDefinition();
        $classDefinition->setName($this->getBehatKey($className));
        $classDefinition->setLayoutDefinitions(
            json_decode('')
        );
        $classDefinition->save();

        $json = '{
            "description": null,
            "parentClass": null,
            "useTraits": null,
            "allowInherit": false,
            "allowVariants": false,
            "showVariants": false,
            "layoutDefinitions": {
                "fieldtype": "panel",
                "labelWidth": 100,
                "layout": null,
                "name": "pimcore_root",
                "type": null,
                "region": null,
                "title": null,
                "width": null,
                "height": null,
                "collapsible": false,
                "collapsed": null,
                "bodyStyle": null,
                "datatype": "layout",
                "permissions": null,
                "childs": [
                    {
                        "fieldtype": "panel",
                        "labelWidth": 100,
                        "layout": null,
                        "name": "Layout",
                        "type": null,
                        "region": null,
                        "title": null,
                        "width": null,
                        "height": null,
                        "collapsible": false,
                        "collapsed": null,
                        "bodyStyle": null,
                        "datatype": "layout",
                        "permissions": null,
                        "childs": [],
                        "locked": false
                    }
                ],
                "locked": false
            },
            "icon": null,
            "previewUrl": null,
            "group": null,
            "linkGeneratorReference": null,
            "propertyVisibility": {
                "grid": {
                    "id": true,
                    "path": true,
                    "published": true,
                    "modificationDate": true,
                    "creationDate": true
                },
                "search": {
                    "id": true,
                    "path": true,
                    "published": true,
                    "modificationDate": true,
                    "creationDate": true
                }
            }
        }';

        ClassDefinition\Service::importClassDefinitionFromJson($classDefinition, $json, true);

        $this->sharedStorage->set('pimcore_definition_name', $classDefinition->getName());
        $this->sharedStorage->set('pimcore_definition_class', get_class($classDefinition));
    }

    /**
     * @Given /^there is a pimcore brick "([^"]+)"$/
     */
    public function createBrickNamed($brickName)
    {
        $brickDefinition = new Objectbrick\Definition();
        $brickDefinition->setKey($this->getBehatKey($brickName));
        $brickDefinition->save();

        $json = '{
            "classDefinitions": [],
            "parentClass": "",
            "layoutDefinitions": {
                "fieldtype": "panel",
                "labelWidth": 100,
                "layout": null,
                "name": null,
                "type": null,
                "region": null,
                "title": null,
                "width": null,
                "height": null,
                "collapsible": false,
                "collapsed": null,
                "bodyStyle": null,
                "datatype": "layout",
                "permissions": null,
                "childs": [
                    {
                        "fieldtype": "panel",
                        "labelWidth": 100,
                        "layout": null,
                        "name": "Layout",
                        "type": null,
                        "region": null,
                        "title": null,
                        "width": null,
                        "height": null,
                        "collapsible": false,
                        "collapsed": null,
                        "bodyStyle": null,
                        "datatype": "layout",
                        "permissions": null,
                        "childs": [],
                        "locked": false
                    }
                ],
                "locked": false
            }
        }';

        ClassDefinition\Service::importObjectBrickFromJson($brickDefinition, $json, true);

        $this->sharedStorage->set('pimcore_definition_name', $brickDefinition->getKey());
        $this->sharedStorage->set('pimcore_definition_class', get_class($brickDefinition));
    }

    /**
     * @Given /^there is a pimcore field-collection "([^"]+)"$/
     */
    public function createCollectionNamed($collection)
    {
        $collectionDefinition = new Fieldcollection\Definition();
        $collectionDefinition->setKey($this->getBehatKey($collection));
        $collectionDefinition->save();

        $json = '{
            "parentClass": "",
            "layoutDefinitions": {
                "fieldtype": "panel",
                "labelWidth": 100,
                "layout": null,
                "name": null,
                "type": null,
                "region": null,
                "title": null,
                "width": null,
                "height": null,
                "collapsible": false,
                "collapsed": null,
                "bodyStyle": null,
                "datatype": "layout",
                "permissions": null,
                "childs": [
                    {
                        "fieldtype": "panel",
                        "labelWidth": 100,
                        "layout": null,
                        "name": "Layout",
                        "type": null,
                        "region": null,
                        "title": null,
                        "width": null,
                        "height": null,
                        "collapsible": false,
                        "collapsed": null,
                        "bodyStyle": null,
                        "datatype": "layout",
                        "permissions": null,
                        "childs": [],
                        "locked": false
                    }
                ],
                "locked": false
            }
        }';

        ClassDefinition\Service::importFieldCollectionFromJson($collectionDefinition, $json, true);

        $this->sharedStorage->set('pimcore_definition_name', $collectionDefinition->getKey());
        $this->sharedStorage->set('pimcore_definition_class', get_class($collectionDefinition));
    }

    /**
     * @Given /^the (definition) has a input field "([^"]+)"$/
     */
    public function definitionHasInputField($definition, $name)
    {
        $jsonDefinition = sprintf('
            {
                "fieldtype": "input",
                "width": null,
                "queryColumnType": "varchar",
                "columnType": "varchar",
                "columnLength": 190,
                "phpdocType": "string",
                "regex": "",
                "name": "%s",
                "title": "%s",
                "tooltip": "",
                "mandatory": false,
                "noteditable": false,
                "index": false,
                "locked": false,
                "style": "",
                "permissions": null,
                "datatype": "data",
                "relationType": false,
                "invisible": false,
                "visibleGridView": true,
                "visibleSearch": true
            }
        ', $name, $name);

        $this->addFieldDefinitionToDefinition($definition, $jsonDefinition);
    }

    /**
     * @param $definition
     * @param $fieldDefinition
     * @throws \CoreShop\Component\Pimcore\ClassDefinitionNotFoundException
     */
    private function addFieldDefinitionToDefinition($definition, $fieldDefinition)
    {
        $definitionUpdater = null;

        if ($definition instanceof ClassDefinition) {
            $definitionUpdater = new ClassUpdate($definition->getName());
        } elseif ($definition instanceof Objectbrick\Definition) {
            $definitionUpdater = new BrickDefinitionUpdate($definition->getKey());
        } elseif ($definition instanceof Fieldcollection\Definition) {
            $definitionUpdater = new FieldCollectionDefinitionUpdate($definition->getKey());
        } else {
            throw new \InvalidArgumentException('Definition Updater not yet implemented');
        }

        if (!$definitionUpdater instanceof ClassUpdateInterface) {
            throw new \InvalidArgumentException('Invalid Definition Updater Class given');
        }

        $definitionUpdater->insertField(json_decode($fieldDefinition));
        $definitionUpdater->save();
    }

    /**
     * @param $key
     * @return string
     */
    private function getBehatKey($key)
    {
        return sprintf('Behat%s', ucfirst($key));
    }
}