<?php

namespace CoreShop\Bundle\IndexBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends ResourceController {

    public function getTypesAction() {
        $types = $this->getWorkerTypes();

        $typesObject = [];

        foreach ($types as $type) {
            $typesObject[] = [
                'name' => $type,
            ];
        }

        return $this->viewHandler->handle($typesObject);
    }

    public function getConfigAction() {
        $interpreters = $this->getInterpreterTypes();
        $interpretersResult = [];

        $getters = $this->getGetterTypes();
        $gettersResult = [];

        foreach ($getters as $getter) {
            $gettersResult[] = [
                'type' => $getter,
                'name' => $getter,
            ];
        }

        foreach ($interpreters as $interpreter) {
            $interpretersResult[] = [
                'type' => $interpreter,
                'name' => $interpreter,
            ];
        }

        $fieldTypes = [
            IndexColumnInterface::FIELD_TYPE_STRING,
            IndexColumnInterface::FIELD_TYPE_DOUBLE,
            IndexColumnInterface::FIELD_TYPE_INTEGER,
            IndexColumnInterface::FIELD_TYPE_BOOLEAN,
            IndexColumnInterface::FIELD_TYPE_DATE,
            IndexColumnInterface::FIELD_TYPE_TEXT
        ];
        $fieldTypesResult = [];

        foreach ($fieldTypes as $type) {
            $fieldTypesResult[] = [
                'type' => $type,
                'name' => ucfirst(strtolower($type)),
            ];
        }

        $productClass = $this->getParameter('coreshop.model.product.class');

        return $this->viewHandler->handle(
            [
                'success' => true,
                'interpreters' => $interpretersResult,
                'getters' => $gettersResult,
                'fieldTypes' => $fieldTypesResult,
                'class' => str_replace('Pimcore\\Model\\Object\\', '', $productClass)
            ]
        );
    }

    public function getClassDefinitionForFieldSelectionAction(Request $request)
    {
        $class = \Pimcore\Model\Object\ClassDefinition::getByName(intval($request->get('class')));
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

        return $this->viewHandler->handle($result);
    }

    /**
     * @return array
     */
    protected function getInterpreterTypes() {
        return $this->getParameter('coreshop.index.interpreters');
    }

    /**
     * @return array
     */
    protected function getGetterTypes() {
        return $this->getParameter('coreshop.index.getters');
    }

    /**
     * @return array
     */
    protected function getWorkerTypes() {
        return $this->getParameter('coreshop.index.workers');
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
}