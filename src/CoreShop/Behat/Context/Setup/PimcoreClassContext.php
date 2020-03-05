<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use CoreShop\Behat\Service\ClassStorageInterface;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Pimcore\DataObject\BrickDefinitionUpdate;
use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use CoreShop\Component\Pimcore\DataObject\ClassUpdateInterface;
use CoreShop\Component\Pimcore\DataObject\FieldCollectionDefinitionUpdate;
use CoreShop\Component\Pimcore\Exception\ClassDefinitionNotFoundException;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Tool;

final class PimcoreClassContext implements Context
{
    private $sharedStorage;
    private $classStorage;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ClassStorageInterface $classStorage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->classStorage = $classStorage;
    }

    /**
     * @Given /^I enable inheritance for class "([^"]+)"$/
     */
    public function iEnableInheritanceForPimcoreClass($className)
    {
        $definitionUpdater = new ClassUpdate($className);
        $definitionUpdater->setProperty('allowInherit', true);
        $definitionUpdater->save();
    }

    /**
     * @Given /^I enable variants for class "([^"]+)"$/
     */
    public function iEnableVariantsForPimcoreClass($className)
    {
        $definitionUpdater = new ClassUpdate($className);
        $definitionUpdater->setProperty('allowVariants', true);
        $definitionUpdater->save();
    }

    /**
     * @Given /^I enable pimcore inheritance$/
     */
    public function enableInheritance()
    {
        DataObject\AbstractObject::setGetInheritedValues(true);
    }

    /**
     * @Given /^I disable pimcore inheritance$/
     */
    public function disableInheritance()
    {
        DataObject\AbstractObject::setGetInheritedValues(false);
    }

    /**
     * @Given /^there is a pimcore class "([^"]+)"$/
     */
    public function createClassNamed($className)
    {
        $name = $this->classStorage->set($className);

        $classDefinition = new ClassDefinition();
        $classDefinition->setName($name);
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

        $this->sharedStorage->set('pimcore_definition_name', $className);
        $this->sharedStorage->set('pimcore_definition_class', get_class($classDefinition));
    }

    /**
     * @Given /^there is a pimcore brick "([^"]+)"$/
     */
    public function createBrickNamed($brickName)
    {
        $name = $this->classStorage->set($brickName);

        $brickDefinition = new Objectbrick\Definition();
        $brickDefinition->setKey($name);
        $brickDefinition->setLayoutDefinitions([]);
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

        $this->sharedStorage->set('pimcore_definition_name', $brickName);
        $this->sharedStorage->set('pimcore_definition_class', get_class($brickDefinition));
    }

    /**
     * @Given /^there is a pimcore field-collection "([^"]+)"$/
     */
    public function createCollectionNamed($collection)
    {
        $name = $this->classStorage->set($collection);

        $collectionDefinition = new Fieldcollection\Definition();
        $collectionDefinition->setKey($name);
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

        $this->sharedStorage->set('pimcore_definition_name', $collection);
        $this->sharedStorage->set('pimcore_definition_class', get_class($collectionDefinition));
    }

    /**
     * @Given /^the (definitions) parent class is set to "([^"]+)"$/
     */
    public function definitionsParentClassIsSetTo($definition, $parentClass)
    {
        $definitionUpdater = $this->getUpdater($definition);
        $definitionUpdater->setProperty('parentClass', $parentClass);
        $definitionUpdater->save();
    }

    /**
     * @Given /^the (definition) is allowed for (behat-class "[^"]+") in field "([^"]+)"$/
     */
    public function theDefinitionIsAllowedForClass($definition, ClassDefinition $class, $field)
    {
        if (!$definition instanceof Objectbrick\Definition) {
            throw new \InvalidArgumentException('This call is only allowed for brick definitions');
        }

        $definitionUpdater = $this->getUpdater($definition);
        $definitionUpdater->setProperty('classDefinitions', [
            [
                'classname' => $class->getName(),
                'fieldname' => $field,
            ],
        ]);
        $definitionUpdater->save();
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
     * @Given /^the (definition) has a href field "([^"]+)"$/
     */
    public function definitionHasHrefField($definition, $name)
    {
        $jsonDefinition = sprintf('
            {
                "fieldtype": "href",
                "width": "",
                "assetUploadPath": "",
                "relationType": true,
                "objectsAllowed": true,
                "assetsAllowed": false,
                "assetTypes": [],
                "documentsAllowed": false,
                "documentTypes": [],
                "lazyLoading": true,
                "classes": [],
                "pathFormatterClass": "",
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
                "columnType": null,
                "invisible": false,
                "visibleGridView": false,
                "visibleSearch": false
            }
        ', $name, $name);

        $this->addFieldDefinitionToDefinition($definition, $jsonDefinition);
    }

    /**
     * @Given /^the (definition) has a checkbox field "([^"]+)"$/
     */
    public function definitionHasCheckboxField($definition, $name)
    {
        $jsonDefinition = sprintf('
            {
                "fieldtype": "checkbox",
                "defaultValue": 0,
                "name": "%s",
                "title": "%s",
                "tooltip": "",
                "mandatory": false,
                "noteditable": true,
                "index": null,
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
     * @Given /^the (definition) has a localized input field "([^"]+)"$/
     */
    public function definitionHasLocalizedInputField($definition, $name)
    {
        $jsonDefinition = sprintf('
            {
                "fieldtype": "localizedfields",
                "phpdocType": "\\Pimcore\\Model\\DataObject\\Localizedfield",
                "childs": [
                    {
                        "fieldtype": "input",
                        "width": null,
                        "columnLength": 190,
                        "regex": "",
                        "unique": false,
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
                        "visibleGridView": false,
                        "visibleSearch": false
                    }
                ],
                "name": "localizedfields",
                "region": null,
                "layout": null,
                "title": null,
                "width": "",
                "height": "",
                "maxTabs": null,
                "labelWidth": null,
                "hideLabelsWhenTabsReached": null,
                "fieldDefinitionsCache": null,
                "tooltip": "",
                "mandatory": false,
                "noteditable": false,
                "index": null,
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
     * @Given /^the (definition) has a localized textarea field "([^"]+)"$/
     */
    public function definitionHasLocalizedTextareaField($definition, $name)
    {
        $jsonDefinition = sprintf('
            {
                "fieldtype": "localizedfields",
                "phpdocType": "\\Pimcore\\Model\\DataObject\\Localizedfield",
                "childs": [
                    {
                        "fieldtype": "textarea",
                        "width": "",
                        "height": "",
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
                        "visibleGridView": false,
                        "visibleSearch": false
                    }
                ],
                "name": "localizedfields",
                "region": null,
                "layout": null,
                "title": null,
                "width": "",
                "height": "",
                "maxTabs": null,
                "labelWidth": null,
                "hideLabelsWhenTabsReached": null,
                "fieldDefinitionsCache": null,
                "tooltip": "",
                "mandatory": false,
                "noteditable": false,
                "index": null,
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
     * @Given /^the (definition) has a brick field "([^"]+)"$/
     */
    public function definitionHasABrickField($definition, $name)
    {
        if (!$definition instanceof ClassDefinition) {
            throw new \InvalidArgumentException(sprintf('Bricks are only allowed in ClassDefinitions, given %s', null !== $definition ? get_class($definition) : 'null'));
        }

        $jsonDefinition = sprintf('
            {
                "fieldtype": "objectbricks",
                "phpdocType": "\\Pimcore\\Model\\DataObject\\Objectbrick",
                "allowedTypes": [],
                "maxItems": "",
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
                "visibleGridView": false,
                "visibleSearch": false
            }
        ', $name, $name);

        $this->addFieldDefinitionToDefinition($definition, $jsonDefinition);
    }

    /**
     * @Given /^the (definition) has a field-collection field "([^"]+)" for (field-collection "[^"]+")$/
     */
    public function definitionHasAFieldCollectionField($definition, $name, Fieldcollection\Definition $fieldCollectionDefinition)
    {
        if (!$definition instanceof ClassDefinition) {
            throw new \InvalidArgumentException(sprintf('Fieldcollections are only allowed in ClassDefinitions, given %s', null !== $definition ? get_class($definition) : 'null'));
        }

        $jsonDefinition = sprintf('
            {
                "fieldtype": "fieldcollections",
                "phpdocType": "\\Pimcore\\Model\\DataObject\\Fieldcollection",
                "allowedTypes": [
                    "%s"
                ],
                "lazyLoading": true,
                "maxItems": "",
                "disallowAddRemove": false,
                "disallowReorder": false,
                "collapsed": false,
                "collapsible": false,
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
                "visibleGridView": false,
                "visibleSearch": false
            }
        ', $fieldCollectionDefinition->getKey(), $name, $name);

        $this->addFieldDefinitionToDefinition($definition, $jsonDefinition);
    }

    /**
     * @Given /^there is an instance of (class|behat-class "[^"]+") with key "([^"]+)"$/
     */
    public function thereIsAnInstanceofClassWithKey(ClassDefinition $definition, $key)
    {
        $className = sprintf('Pimcore\\Model\\DataObject\\%s', $definition->getName());
        /**
         * @var Concrete $instance
         */
        $instance = new $className();
        $instance->setKey($key);
        $instance->setParentId(1);
        $instance->save();

        $this->sharedStorage->set('object-instance', $instance);
    }

    /**
     * @Given /^the (object-instance) is published$/
     */
    public function theObjectInstanceIsPublished(Concrete $instance)
    {
        $instance->setPublished(true);
        $instance->save();
    }

    /**
     * @Given /^the (object-instance) is not published$/
     */
    public function theObjectInstanceIsNotPublished(Concrete $instance)
    {
        $instance->setPublished(false);
        $instance->save();
    }

    /**
     * @Given /the (object-instance) has following values:/
     */
    public function theObjectInstanceHasFollowingValues(Concrete $object, TableNode $table)
    {
        $hash = $table->getHash();

        foreach ($hash as $row) {
            switch ($row['type']) {
                case 'checkbox':
                    $object->setValue($row['key'], filter_var($row['value'], FILTER_VALIDATE_BOOLEAN));

                    break;

                case 'input':
                    $object->setValue($row['key'], $row['value']);

                    break;

                case 'href':
                    $object->setValue($row['key'], DataObject::getById($row['value']));

                    break;

                case 'localized':
                    $setter = 'set' . ucfirst($row['key']);

                    foreach (Tool::getValidLanguages() as $lang) {
                        $object->$setter($row['value'], $lang);
                    }

                    break;

                case 'brick':
                    $config = json_decode(stripslashes($row['value']), true);
                    $type = $this->classStorage->get($config['type']);
                    $className = sprintf('Pimcore\\Model\\DataObject\\Objectbrick\\Data\\%s', $type);

                    $brickInstance = new $className($object);

                    foreach ($config['values'] as $key => $value) {
                        $brickInstance->setValue($key, $value);
                    }

                    $object->{'get' . ucfirst($row['key'])}()->{'set' . ucfirst($type)}($brickInstance);

                    break;

                case 'collection':
                    $config = json_decode(stripslashes($row['value']), true);
                    $type = $this->classStorage->get($config['type']);
                    $className = sprintf('Pimcore\\Model\\DataObject\\Fieldcollection\\Data\\%s', $type);

                    $items = new Fieldcollection();

                    foreach ($config['values'] as $itemValues) {
                        $collectionInstance = new $className();

                        foreach ($itemValues as $key => $value) {
                            $collectionInstance->setValue($key, $value);
                        }

                        $items->add($collectionInstance);
                    }

                    $object->{'set' . ucfirst($row['key'])}($items);

                    break;

                default:
                    throw new \InvalidArgumentException(sprintf('Type %s not yet supported', $row['type']));

                    break;
            }
        }

        $object->save();
    }

    /**
     * @param string $definition
     * @param string $fieldDefinition
     *
     * @throws ClassDefinitionNotFoundException
     */
    private function addFieldDefinitionToDefinition($definition, $fieldDefinition)
    {
        $definitionUpdater = $this->getUpdater($definition);
        $definitionUpdater->insertField(json_decode(stripslashes($fieldDefinition)));
        $definitionUpdater->save();
    }

    /**
     * @param string $definition
     *
     * @return BrickDefinitionUpdate|ClassUpdate|FieldCollectionDefinitionUpdate
     *
     * @throws ClassDefinitionNotFoundException
     */
    private function getUpdater($definition)
    {
        $definitionUpdater = null;

        if ($definition instanceof ClassDefinition) {
            $definitionUpdater = new ClassUpdate($definition->getName());
        } elseif ($definition instanceof Objectbrick\Definition) {
            $definitionUpdater = new BrickDefinitionUpdate($definition->getKey());
        } elseif ($definition instanceof Fieldcollection\Definition) {
            $definitionUpdater = new FieldCollectionDefinitionUpdate($definition->getKey());
        } else {
            throw new \InvalidArgumentException(sprintf('Definition Updater for %s not found', null !== $definition ? get_class($definition) : 'null'));
        }

        if (!$definitionUpdater instanceof ClassUpdateInterface) {
            throw new \InvalidArgumentException(sprintf('Definition Updater for %s not found', null !== $definition ? get_class($definition) : 'null'));
        }

        return $definitionUpdater;
    }
}
