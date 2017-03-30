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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\Controller\Model;

use CoreShop\Bundle\CoreShopLegacyBundle\Controller\Admin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexesController
 *
 * @Route("/indexes")
 */
class IndexesController extends Admin\DataController
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_indexes';

    /**
     * @var string
     */
    protected $model = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index::class;

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model) {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index) {
            $model->setType('mysql');
            $model->setConfig(new \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index\Config());
        }
    }

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     * @return array
     */
    protected function getReturnValues(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model)
    {
        $values = parent::getReturnValues($model);

        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index) {
            $values['classId'] = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product::classId();
        }

        return $values;
    }

    /**
     * @param Request $request
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     * @throws \CoreShop\Bundle\CoreShopLegacyBundle\Exception
     *
     * @Route("/save")
     */
    public function saveAction(Request $request)
    {
        $id = $request->get('id');
        $data = $request->get('data');
        $index = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index::getById($id);

        $prohibitedFieldNames = ["name", "id"];

        if ($data && $index instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index) {
            try {
                $data = \Zend_Json::decode($request->get('data'));

                $indexType = ucfirst($data['type']);

                $configClass = '\\CoreShop\Bundle\CoreShopLegacyBundle\\Model\\Index\\Config\\' . ucfirst($indexType);

                if (\Pimcore\Tool::classExists($configClass)) {
                    $config = new $configClass();

                    if ($config instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index\Config) {
                        $columns = [];

                        foreach ($data['config']['columns'] as $col) {
                            $objectType = ucfirst($col['objectType']);

                            if (!$col['key']) {
                                continue;
                            }

                            $class = null;

                            //Allow Column-Types to be declared in Template and/or Website
                            $columnNamespace = '\\CoreShop\Bundle\CoreShopLegacyBundle\\Model\\Index\\Config\\Column';
                            $columnClass = $columnNamespace . '\\' . $objectType;

                            if (\Pimcore\Tool::classExists($columnClass)) {
                                $class = $columnClass;
                            }

                            if (is_null($class)) {
                                //Use fallback column
                                throw new \CoreShop\Bundle\CoreShopLegacyBundle\Exception('No config implementation for column with type ' . $objectType . ' found');
                            }

                            $columnObject = new $class();

                            if ($columnObject instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index\Config\Column) {
                                $columnObject->setValues($col);

                                if (in_array($columnObject->getName(), $prohibitedFieldNames)) {
                                    throw new \CoreShop\Bundle\CoreShopLegacyBundle\Exception(sprintf('Field Name "%s" is prohibited for indexes', $columnObject->getName()));
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
                        throw new \CoreShop\Bundle\CoreShopLegacyBundle\Exception('Config class for type ' . $data['type'] . ' not instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index\Config');
                    }
                    unset($data['config']);
                } else {
                    throw new \CoreShop\Bundle\CoreShopLegacyBundle\Exception('Config class for type ' . $data['type'] . ' not found');
                }

                //Check for unique fieldnames
                $fieldNames = [];

                foreach ($config->getColumns() as $col) {
                    if (in_array($col->getName(), $fieldNames)) {
                        return $this->json([
                            'success' => false,
                            'message' => sprintf($this->view->translate("Duplicate fieldname '%s' found."), $col->getName()),
                        ]);
                    }

                    $fieldNames[] = $col->getName();
                }

                $index->setValues($data);
                $index->save();

                \CoreShop\Bundle\CoreShopLegacyBundle\IndexService::getIndexService()->getWorker($index->getName())->createOrUpdateIndexStructures();

                return $this->json(['success' => true, 'data' => $index]);
            } catch (Exception $ex) {
                return $this->json(['success' => false, 'message' => $ex->getMessage()]);
            }
        } else {
            return $this->json(['success' => false]);
        }
    }

    /**
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     *
     * @Route("/get-types")
     */
    public function getTypesAction()
    {
        $types = \CoreShop\Bundle\CoreShopLegacyBundle\IndexService::getIndexDispatcher()->getTypeKeys();
        $typesObject = [];

        foreach ($types as $type) {
            $typesObject[] = [
                'name' => $type,
            ];
        }

        return $this->json($typesObject);
    }

    /**
     * @param Request $request
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     *
     * @Route("/get-class-definition-for-selection")
     */
    public function getClassDefinitionForFieldSelectionAction(Request $request)
    {
        $class = \Pimcore\Model\Object\ClassDefinition::getById(intval($request->get('id')));
        $fields = $class->getFieldDefinitions();

        $result = [
            'fields' => [
                'nodeLabel' => 'fields',
                'nodeType' => 'object',
                'childs' => [],
            ],
        ];

        foreach ($fields as $field) {
            if ($field instanceof \Pimcore\Model\Object\ClassDefinition\Data\Localizedfields) {
                if (!is_array($result['localizedfields'])) {
                    $result['localizedfields'] = [
                        'nodeLabel' => 'localizedfields',
                        'nodeType' => 'localizedfields',
                        'childs' => [],
                    ];
                }

                $localizedFields = $field->getFieldDefinitions();

                foreach ($localizedFields as $localizedField) {
                    $result['localizedfields']['childs'][] = $this->getFieldConfiguration($localizedField);
                }
            } elseif ($field instanceof \Pimcore\Model\Object\ClassDefinition\Data\Objectbricks) {
                $list = new \Pimcore\Model\Object\Objectbrick\Definition\Listing();
                $list = $list->load();

                foreach ($list as $brickDefinition) {
                    if ($brickDefinition instanceof \Pimcore\Model\Object\Objectbrick\Definition) {
                        $key = $brickDefinition->getKey();
                        $classDefs = $brickDefinition->getClassDefinitions();

                        foreach ($classDefs as $classDef) {
                            if ($classDef['classname'] === $class->getId()) {
                                $fields = $brickDefinition->getFieldDefinitions();

                                $result[$key] = [];
                                $result[$key]['nodeLabel'] = $key;
                                $result[$key]['className'] = $key;
                                $result[$key]['nodeType'] = 'objectbricks';
                                $result[$key]['childs'] = [];

                                foreach ($fields as $field) {
                                    $result[$key]['childs'][] = $this->getFieldConfiguration($field);
                                }

                                break;
                            }
                        }
                    }
                }
            } elseif ($field instanceof \Pimcore\Model\Object\ClassDefinition\Data\Fieldcollections) {
                foreach ($field->getAllowedTypes() as $type) {
                    $definition = \Pimcore\Model\Object\Fieldcollection\Definition::getByKey($type);

                    $fieldDefinition = $definition->getFieldDefinitions();

                    $key = $definition->getKey();

                    $result[$key] = [];
                    $result[$key]["nodeLabel"] = $key;
                    $result[$key]["className"] = $key;
                    $result[$key]["nodeType"] = "fieldcollections";
                    $result[$key]['childs'] = [];

                    foreach ($fieldDefinition as $fieldcollectionField) {
                        $result[$key]['childs'][] = $this->getFieldConfiguration($fieldcollectionField);
                    }
                }
            } elseif ($field instanceof \Pimcore\Model\Object\ClassDefinition\Data\Classificationstore) {
                $list = new \Pimcore\Model\Object\Classificationstore\GroupConfig\Listing();

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

        return $this->json($result);
    }

    /**
     * @param \Pimcore\Model\Object\Classificationstore\GroupConfig $config
     * @return array
     */
    protected function getClassificationStoreGroupConfiguration(\Pimcore\Model\Object\Classificationstore\GroupConfig $config)
    {
        $result = [];
        $result['nodeLabel'] = $config->getName();
        $result['nodeType'] = 'classificationstore';
        $result['childs'] = [];

        foreach ($config->getRelations() as $relation) {
            if ($relation instanceof \Pimcore\Model\Object\Classificationstore\KeyGroupRelation) {
                $keyId = $relation->getKeyId();

                $keyConfig = \Pimcore\Model\Object\Classificationstore\KeyConfig::getById($keyId);

                $result['childs'][] = $this->getClassificationStoreFieldConfiguration($keyConfig, $config);
            }
        }

        return $result;
    }

    /**
     * @param \Pimcore\Model\Object\ClassDefinition\Data $field
     * @return array
     */
    protected function getFieldConfiguration(\Pimcore\Model\Object\ClassDefinition\Data $field)
    {
        return [
            'name' => $field->getName(),
            'fieldtype' => $field->getFieldtype(),
            'title' => $field->getTitle(),
            'tooltip' => $field->getTooltip(),
        ];
    }

    /**
     * @param \Pimcore\Model\Object\Classificationstore\KeyConfig $field
     * @param \Pimcore\Model\Object\Classificationstore\GroupConfig $groupConfig
     * @return array
     */
    protected function getClassificationStoreFieldConfiguration(\Pimcore\Model\Object\Classificationstore\KeyConfig $field, \Pimcore\Model\Object\Classificationstore\GroupConfig $groupConfig)
    {
        return [
            'name' => $field->getName(),
            'fieldtype' => $field->getType(),
            'title' => $field->getName(),
            'tooltip' => $field->getDescription(),
            'keyConfigId' => $field->getId(),
            'groupConfigId' => $groupConfig->getId(),
        ];
    }

    /**
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     *
     * @Route("/get-config")
     */
    public function getConfigAction()
    {
        $interpreters = \CoreShop\Bundle\CoreShopLegacyBundle\IndexService::getInterpreterDispatcher()->getTypeKeys();
        $interpretersResult = [];

        foreach ($interpreters as $interpreter) {
            $interpretersResult[] = [
                'type' => $interpreter,
                'name' => $interpreter,
            ];
        }

        $getters = \CoreShop\Bundle\CoreShopLegacyBundle\IndexService::getGetterDispatcher()->getTypeKeys();
        $gettersResult = [];

        foreach ($getters as $getter) {
            $gettersResult[] = [
                'type' => $getter,
                'name' => $getter,
            ];
        }

        $fieldTypes = [
            \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index\Config\Column::FIELD_TYPE_STRING,
            \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index\Config\Column::FIELD_TYPE_INTEGER,
            \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index\Config\Column::FIELD_TYPE_DOUBLE,
            \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index\Config\Column::FIELD_TYPE_BOOLEAN,
            \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index\Config\Column::FIELD_TYPE_DATE,
            \CoreShop\Bundle\CoreShopLegacyBundle\Model\Index\Config\Column::FIELD_TYPE_TEXT
        ];
        $fieldTypesResult = [];

        foreach ($fieldTypes as $type) {
            $fieldTypesResult[] = [
                'type' => $type,
                'name' => ucfirst(strtolower($type)),
            ];
        }

        return $this->json([
            'success' => true,
            'interpreters' => $interpretersResult,
            'getters' => $gettersResult,
            'fieldTypes' => $fieldTypesResult
        ]);
    }
}
