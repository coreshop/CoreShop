<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Model\Index;
use CoreShop\Controller\Action\Admin;
use Pimcore\Model\Object;

/**
 * Class CoreShop_Admin_IndexesController
 */
class CoreShop_Admin_IndexesController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array('list');

        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_indexes');
        }
    }

    public function listAction()
    {
        $list = Index::getList();

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
            'id' => $index->getId(),
            'text' => $index->getName(),
            'qtipCfg' => array(
                'title' => 'ID: '.$index->getId(),
            ),
            'name' => $index->getName(),
        );
        
        return $tmp;
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        if (strlen($name) <= 0) {
            $this->helper->json(array('success' => false, 'message' => $this->getTranslator()->translate('Name must be set')));
        } else {
            $group = new Index();
            $group->setName($name);
            $group->setType('mysql');
            $group->setConfig(array());
            $group->save();

            $this->_helper->json(array('success' => true, 'data' => $group));
        }
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $group = Index::getById($id);

        if ($group instanceof Index) {
            $data = get_object_vars($group);
            $data['classId'] = \CoreShop\Model\Product::classId();

            $this->_helper->json(array('success' => true, 'data' => $data));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $index = Index::getById($id);

        $prohibitedFieldNames = ["name", "id"];

        if ($data && $index instanceof Index) {
            try {
                $data = \Zend_Json::decode($this->getParam('data'));

                $indexType = ucfirst($data['type']);

                $configClass = '\\CoreShop\\Model\\Index\\Config\\' . ucfirst($indexType);

                if (\Pimcore\Tool::classExists($configClass)) {
                    $config = new $configClass();

                    if ($config instanceof \CoreShop\Model\Index\Config) {
                        $columns = array();

                        foreach ($data['config']['columns'] as $col) {
                            $objectType = ucfirst($col['objectType']);

                            if (!$col['key']) {
                                continue;
                            }

                            $class = null;

                            //Allow Column-Types to be declared in Template and/or Website
                            $columnNamespace = '\\CoreShop\\Model\\Index\\Config\\Column\\';
                            $columnClass = $columnNamespace . $indexType . '\\' . $objectType;

                            if (\Pimcore\Tool::classExists($columnClass)) {
                                $class = $columnClass;
                            }

                            if (!$class) {
                                //Use fallback column
                                throw new \CoreShop\Exception('No config implementation for column with type ' . $objectType . ' found');
                            }

                            $columnObject = new $class();

                            if ($columnObject instanceof \CoreShop\Model\Index\Config\Column\AbstractColumn) {
                                $columnObject->setValues($col);

                                if (in_array($columnObject->getName(), $prohibitedFieldNames)) {
                                    throw new \CoreShop\Exception(sprintf('Field Name "%s" is prohibited for indexes', $columnObject->getName()));
                                }

                                $columnObject->validate();

                                $columns[] = $columnObject;
                            }
                        }

                        unset($data['config']['columns']);

                        $config->setValues($data['config']);
                        $config->setColumns($columns);

                        $index->setConfig($config);
                    } else {
                        throw new \CoreShop\Exception('Config class for type ' . $data['type'] . ' not instanceof \CoreShop\Model\Index\Config');
                    }
                    unset($data['config']);
                } else {
                    throw new \CoreShop\Exception('Config class for type ' . $data['type'] . ' not found');
                }

                //Check for unique fieldnames
                $fieldNames = array();

                foreach ($config->getColumns() as $col) {
                    if (in_array($col->getName(), $fieldNames)) {
                        $this->_helper->json(array(
                            'success' => false,
                            'message' => sprintf($this->view->translate("Duplicate fieldname '%s' found."), $col->getName()),
                        ));
                    }

                    $fieldNames[] = $col->getName();
                }

                $index->setValues($data);
                $index->save();

                \CoreShop\IndexService::getIndexService()->getWorker($index->getName())->createOrUpdateIndexStructures();

                $this->_helper->json(array('success' => true, 'data' => $index));
            } catch (Exception $ex) {
                $this->_helper->json(array('success' => false, 'message' => $ex->getMessage()));
            }
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $group = Index::getById($id);

        if ($group instanceof Index) {
            $group->delete();

            $this->_helper->json(array('success' => true));
        }

        $this->_helper->json(array('success' => false));
    }

    public function getTypesAction()
    {
        $types = \CoreShop\IndexService::getTypes();
        $typesObject = array();

        foreach ($types as $type) {
            $typesObject[] = array(
                'name' => $type,
            );
        }

        $this->_helper->json($typesObject);
    }

    public function getClassDefinitionForFieldSelectionAction()
    {
        $class = Object\ClassDefinition::getById(intval($this->getParam('id')));
        $fields = $class->getFieldDefinitions();

        $result = array(
            'fields' => array(
                'nodeLabel' => 'fields',
                'nodeType' => 'object',
                'childs' => array(),
            ),
        );

        foreach ($fields as $field) {
            if ($field instanceof Object\ClassDefinition\Data\Localizedfields) {
                if (!is_array($result['localizedfields'])) {
                    $result['localizedfields'] = array(
                        'nodeLabel' => 'localizedfields',
                        'nodeType' => 'localizedfields',
                        'childs' => array(),
                    );
                }

                $localizedFields = $field->getFieldDefinitions();

                foreach ($localizedFields as $localizedField) {
                    $result['localizedfields']['childs'][] = $this->getFieldConfiguration($localizedField);
                }
            } elseif ($field instanceof Object\ClassDefinition\Data\Objectbricks) {
                $list = new Object\Objectbrick\Definition\Listing();
                $list = $list->load();

                foreach ($list as $brickDefinition) {
                    if ($brickDefinition instanceof Object\Objectbrick\Definition) {
                        $key = $brickDefinition->getKey();
                        $classDefs = $brickDefinition->getClassDefinitions();

                        foreach ($classDefs as $classDef) {
                            if ($classDef['classname'] === $class->getId()) {
                                $fields = $brickDefinition->getFieldDefinitions();

                                $result[$key] = array();
                                $result[$key]['nodeLabel'] = $key;
                                $result[$key]['className'] = $key;
                                $result[$key]['nodeType'] = 'objectbricks';
                                $result[$key]['childs'] = array();

                                foreach ($fields as $field) {
                                    $result[$key]['childs'][] = $this->getFieldConfiguration($field);
                                }

                                break;
                            }
                        }
                    }
                }
            } elseif ($field instanceof Object\ClassDefinition\Data\Fieldcollections) {
                //TODO: implement FieldCollection

                foreach ($field->getAllowedTypes() as $type) {
                    $definition = Object\Fieldcollection\Definition::getByKey($type);

                    $fieldDefinition = $definition->getFieldDefinitions();

                    $key = $definition->getKey();

                    $result[$key] = array();
                    $result[$key]["nodeLabel"] = $key;
                    $result[$key]["className"] = $key;
                    $result[$key]["nodeType"] = "fieldcollections";
                    $result[$key]['childs'] = array();

                    foreach ($fieldDefinition as $fieldcollectionField) {
                        $result[$key]['childs'][] = $this->getFieldConfiguration($fieldcollectionField);
                    }
                }
            } elseif ($field instanceof Object\ClassDefinition\Data\Classificationstore) {
                $list = new Object\Classificationstore\GroupConfig\Listing();

                $allowedGroupIds = $field->getAllowedGroupIds();

                if ($allowedGroupIds) {
                    $list->setCondition('ID in ('.implode(',', $allowedGroupIds).')');
                }

                $list->load();

                $groupConfigList = $list->getList();

                foreach ($groupConfigList as $config) {
                    $key = $config->getId().($config->getName() ? $config->getName() : 'EMPTY');

                    $result[$key] = $this->getClassificationStoreGroupConfiguration($config);
                }
            } else {
                $result['fields']['childs'][] = $this->getFieldConfiguration($field);
            }
        }

        $this->_helper->json($result);
    }

    protected function getClassificationStoreGroupConfiguration(Object\Classificationstore\GroupConfig $config)
    {
        $result = array();
        $result['nodeLabel'] = $config->getName();
        $result['nodeType'] = 'classificationstore';
        $result['childs'] = array();

        foreach ($config->getRelations() as $relation) {
            if ($relation instanceof Object\Classificationstore\KeyGroupRelation) {
                $keyId = $relation->getKeyId();

                $keyConfig = Object\Classificationstore\KeyConfig::getById($keyId);

                $result['childs'][] = $this->getClassificationStoreFieldConfiguration($keyConfig, $config);
            }
        }

        return $result;
    }

    protected function getFieldConfiguration(Object\ClassDefinition\Data $field)
    {
        return array(
            'name' => $field->getName(),
            'fieldtype' => $field->getFieldtype(),
            'title' => $field->getTitle(),
            'tooltip' => $field->getTooltip(),
        );
    }

    protected function getClassificationStoreFieldConfiguration(Object\Classificationstore\KeyConfig $field, Object\Classificationstore\GroupConfig $groupConfig)
    {
        return array(
            'name' => $field->getName(),
            'fieldtype' => $field->getType(),
            'title' => $field->getName(),
            'tooltip' => $field->getDescription(),
            'keyConfigId' => $field->getId(),
            'groupConfigId' => $groupConfig->getId(),
        );
    }

    public function getAvailableGettersAction()
    {
        $getters = \CoreShop\IndexService\Getter\AbstractGetter::getGetters();
        $result = array();

        foreach ($getters as $getter) {
            $result[] = array(
                'type' => $getter,
                'name' => $getter,
            );
        }

        $this->_helper->json(array(
            'success' => true,
            'data' => $result,
        ));
    }

    public function getAvailableInterpretersAction()
    {
        $interpreters = \CoreShop\IndexService\Interpreter\AbstractInterpreter::getInterpreters();
        $result = array();

        foreach ($interpreters as $interpreter) {
            $result[] = array(
                'type' => $interpreter,
                'name' => $interpreter,
            );
        }

        $this->_helper->json(array(
            'success' => true,
            'data' => $result,
        ));
    }
}
