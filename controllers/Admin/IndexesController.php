<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Plugin;
use CoreShop\Tool;
use CoreShop\Model\Index;
use Pimcore\Controller\Action\Admin;
use Pimcore\Model\Object;

class CoreShop_Admin_IndexesController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array("list");

        if (!in_array($this->getParam("action"), $notRestrictedActions)) {
            $this->checkPermission("coreshop_permission_indexes");
        }
    }

    public function listAction()
    {
        $list = new Index\Listing();

        $data = array();
        if (is_array($list->getData())) {
            foreach ($list->getData() as $group) {
                $data[] = $this->getTreeNodeConfig($group);
            }
        }
        $this->_helper->json($data);
    }

    protected function getTreeNodeConfig(Index $index)
    {
        $tmp = array(
            "id" => $index->getId(),
            "text" => $index->getName(),
            "elementType" => "group",
            "qtipCfg" => array(
                "title" => "ID: " . $index->getId()
            ),
            "name" => $index->getName()
        );

        $tmp["leaf"] = true;
        $tmp["iconCls"] = "coreshop_icon_indexes";
        $tmp["allowChildren"] = false;

        return $tmp;
    }

    public function addAction()
    {
        $name = $this->getParam("name");

        if (strlen($name) <= 0) {
            $this->helper->json(array("success" => false, "message" => $this->getTranslator()->translate("Name must be set")));
        } else {
            $group = new Index();
            $group->setName($name);
            $group->setType("mysql");
            $group->setConfig(array());
            $group->save();

            $this->_helper->json(array("success" => true, "data" => $group));
        }
    }

    public function getAction()
    {
        $id = $this->getParam("id");
        $group = Index::getById($id);

        if ($group instanceof Index) {
            $data = get_object_vars($group);
            $data['classId'] = \Pimcore\Model\Object\CoreShopProduct::classId();

            $this->_helper->json(array("success" => true, "data" => $data));
        } else {
            $this->_helper->json(array("success" => false));
        }
    }

    public function saveAction()
    {
        $id = $this->getParam("id");
        $data = $this->getParam("data");
        $index = Index::getById($id);

        if ($data && $index instanceof Index) {
            $data = \Zend_Json::decode($this->getParam("data"));

            $indexType = ucfirst($data['type']);

            $configClass = "\\CoreShop\\Model\\Index\\Config\\" . ucfirst($indexType);

            if(\Pimcore\Tool::classExists($configClass)) {
                $config = new $configClass();

                if($config instanceof \CoreShop\Model\Index\Config) {
                    $columns = array();

                    foreach ($data['config']['columns'] as $col) {
                        $objectType = ucfirst($col['objectType']);

                        $columnNamespace = "\\CoreShop\\Model\\Index\\Config\\Column\\";
                        $columnClass = $columnNamespace . $indexType . "\\" . $objectType;

                        if (!\Pimcore\Tool::classExists($columnClass)) {
                            //Use fallback column
                            throw new \Exception("No config implementation for column with type " . $objectType . " found");
                        }

                        $columnObject = new $columnClass();

                        if($columnObject instanceof \CoreShop\Model\Index\Config\Column\AbstractColumn) {
                            $columnObject->setValues($col);

                            $columns[] = $columnObject;
                        }
                    }

                    unset($data['config']['columns']);

                    $config->setValues($data['config']);
                    $config->setColumns($columns);

                    $index->setConfig($config);
                }
                else {
                    throw new \Exception("Config class for type " . $data['type'] . ' not instanceof \CoreShop\Model\Index\Config');
                }
                unset($data['config']);
            }
            else {
                throw new \Exception("Config class for type " . $data['type'] . ' not found');
            }


            $index->setValues($data);
            $index->save();

            \CoreShop\IndexService::getIndexService()->getWorker($index->getName())->createOrUpdateIndexStructures();

            $this->_helper->json(array("success" => true, "data" => $index));
        } else {
            $this->_helper->json(array("success" => false));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam("id");
        $group = Index::getById($id);

        if ($group instanceof Index) {
            $group->delete();

            $this->_helper->json(array("success" => true));
        }

        $this->_helper->json(array("success" => false));
    }

    public function getTypesAction() {
        $types = \CoreShop\IndexService::getTypes();
        $typesObject = array();

        foreach($types as $type) {
            $typesObject[] = array(
                "name" => $type
            );
        }

        $this->_helper->json($typesObject);
    }

    public function getClassDefinitionForFieldSelectionAction()
    {
        $class = Object\ClassDefinition::getById(intval($this->getParam("id")));

        /*$layoutDefinitions = $class->getLayoutDefinitions();

        $class->setFieldDefinitions(null);

        $result = array();

        $result['objectColumns']['childs'] = $layoutDefinitions->getChilds();
        $result['objectColumns']['nodeLabel'] = "object_columns";
        $result['objectColumns']['nodeType'] = "object";

        // array("id", "fullpath", "published", "creationDate", "modificationDate", "filename", "classname");
        $systemColumnNames = Object\Concrete::$systemColumnNames;
        $systemColumns = array();
        foreach ($systemColumnNames as $systemColumn) {
            $systemColumns[] = array("title" => $systemColumn, "name" => $systemColumn, "datatype" => "data", "fieldtype" => "system");
        }
        $result['systemColumns']['nodeLabel'] = "system_columns";
        $result['systemColumns']['nodeType'] = "system";
        $result['systemColumns']['childs'] = $systemColumns;

        $list = new Object\Objectbrick\Definition\Listing();
        $list = $list->load();

        foreach ($list as $brickDefinition) {
            $classDefs = $brickDefinition->getClassDefinitions();
            if (!empty($classDefs)) {
                foreach ($classDefs as $classDef) {
                    if ($classDef['classname'] == $class->getId()) {
                        $key = $brickDefinition->getKey();
                        $result[$key]['nodeLabel'] = $key;
                        $result[$key]['nodeType'] = "objectbricks";
                        $result[$key]['childs'] = $brickDefinition->getLayoutdefinitions()->getChilds();
                        break;
                    }
                }
            }
        }*/

        $fields = $class->getFieldDefinitions();

        $result = array(
            "fields" => array(
                "nodeLabel" => "fields",
                "nodeType" => "object",
                "childs" => array()
            )
        );

        foreach($fields as $field) {
            if($field instanceof Object\ClassDefinition\Data\Localizedfields) {
                $localizedFields = $field->getFieldDefinitions();

                foreach($localizedFields as $localizedField) {
                    $result['fields']["childs"][] = $this->getFieldConfiguration($localizedField);

                }
            }
            else if($field instanceof Object\ClassDefinition\Data\Objectbricks) {
                $list = new Object\Objectbrick\Definition\Listing();
                $list = $list->load();

                foreach ($list as $brickDefinition) {
                    if($brickDefinition instanceof Object\Objectbrick\Definition) {

                        $key = $brickDefinition->getKey();
                        $classDefs = $brickDefinition->getClassDefinitions();

                        foreach($classDefs as $classDef) {
                            if($classDef['classname'] === $class->getId()) {
                                $fields = $brickDefinition->getFieldDefinitions();

                                $result[$key] = array();
                                $result[$key]['nodeLabel'] = $key;
                                $result[$key]['class'] = $key;
                                $result[$key]['nodeType'] = "objectbricks";
                                $result[$key]['childs'] = array();

                                foreach($fields as $field)
                                {
                                    $result[$key]['childs'][] = $this->getFieldConfiguration($field);
                                }

                                break;
                            }
                        }
                    }
                }
            }
            else if($field instanceof Object\ClassDefinition\Data\Fieldcollections) {
                //TODO: implement FieldCollection
            }
            else if($field instanceof Object\ClassDefinition\Data\Classificationstore) {
                $list = new Object\Classificationstore\GroupConfig\Listing();

                $allowedGroupIds = $field->getAllowedGroupIds();

                if ($allowedGroupIds) {
                    $list->setCondition("ID in (" . implode(",", $allowedGroupIds) . ")");
                }

                $list->load();

                $groupConfigList = $list->getList();

                foreach ($groupConfigList as $config) {
                    $key = $config->getId() . ($config->getName() ? $config->getName() : "EMPTY");

                    $result[$key] = $this->getClassificationStoreGroupConfiguration($config);
                }
            }
            else {
                $result['fields']["childs"][] = $this->getFieldConfiguration($field);
            }
        }

        $this->_helper->json($result);
    }

    protected function getClassificationStoreGroupConfiguration(Object\Classificationstore\GroupConfig $config) {
        $result = array();
        $result['nodeLabel'] = $config->getName();
        $result['nodeType'] = "classificationstore";
        $result['childs'] = array();

        foreach($config->getRelations() as $relation) {
            if($relation instanceof Object\Classificationstore\KeyGroupRelation) {
                $keyId = $relation->getKeyId();

                $keyConfig = Object\Classificationstore\KeyConfig::getById($keyId);

                $result['childs'][] = $this->getClassificationStoreFieldConfiguration($keyConfig, $config);
            }
        }

        return $result;
    }

    protected function getFieldConfiguration(Object\ClassDefinition\Data $field) {
        return array(
            "name" => $field->getName(),
            "fieldtype" => $field->getFieldtype(),
            "title" => $field->getTitle(),
            "tooltip" => $field->getTooltip()
        );
    }

    protected function getClassificationStoreFieldConfiguration(Object\Classificationstore\KeyConfig $field, Object\Classificationstore\GroupConfig $groupConfig) {
        return array(
            "name" => $field->getName(),
            "fieldtype" => $field->getType(),
            "title" => $field->getName(),
            "tooltip" => $field->getDescription(),
            "keyConfigId" => $field->getId(),
            "groupConfigId" => $groupConfig->getId()
        );
    }
}