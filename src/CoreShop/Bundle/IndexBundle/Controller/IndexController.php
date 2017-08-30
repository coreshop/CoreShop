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

namespace CoreShop\Bundle\IndexBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends ResourceController
{
    /**
     * Get Worker Types.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getTypesAction()
    {
        $types = $this->getWorkerTypes();

        $typesObject = [];

        foreach ($types as $type) {
            $typesObject[] = [
                'name' => $type,
            ];
        }

        return $this->viewHandler->handle($typesObject);
    }

    /**
     * Get Index Configurations.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getConfigAction()
    {
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
            IndexColumnInterface::FIELD_TYPE_TEXT,
        ];
        $fieldTypesResult = [];

        foreach ($fieldTypes as $type) {
            $fieldTypesResult[] = [
                'type' => $type,
                'name' => ucfirst(strtolower($type)),
            ];
        }

        $classes = new DataObject\ClassDefinition\Listing();
        $classes = $classes->load();
        $availableClasses = [];

        foreach ($classes as $class) {
            if ($class instanceof DataObject\ClassDefinition) {
                $pimcoreClass = 'Pimcore\Model\DataObject\\' . ucfirst($class->getName());

                if (in_array(IndexableInterface::class, class_implements($pimcoreClass), true)) {
                    $availableClasses[] = [
                        'name' => $class->getName()
                    ];
                }
            }
        }

        return $this->viewHandler->handle(
            [
                'success' => true,
                'interpreters' => $interpretersResult,
                'getters' => $gettersResult,
                'fieldTypes' => $fieldTypesResult,
                'classes' => $availableClasses
            ]
        );
    }

    /**
     * Get Pimcore Class Definition.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getClassDefinitionForFieldSelectionAction(Request $request)
    {
        $class = DataObject\ClassDefinition::getByName($request->get('class'));

        if (!$class instanceof DataObject\ClassDefinition) {
            return $this->viewHandler->handle([]);
        }

        $fields = $class->getFieldDefinitions();

        $result = [
            'fields' => [
                'nodeLabel' => 'fields',
                'nodeType' => 'object',
                'childs' => [],
            ]
        ];

        $result = array_merge_recursive($result, $this->getSystemFields());

        foreach ($fields as $field) {
            if ($field instanceof DataObject\ClassDefinition\Data\Localizedfields) {
                $result = array_merge_recursive($result, $this->getLocalizedFields($field));
            } elseif ($field instanceof DataObject\ClassDefinition\Data\Objectbricks) {
                $result = array_merge_recursive($result, $this->getObjectbrickFields($field, $class));
            } elseif ($field instanceof DataObject\ClassDefinition\Data\Fieldcollections) {
                $result = array_merge_recursive($result, $this->getFieldcollectionFields($field));
            } elseif ($field instanceof DataObject\ClassDefinition\Data\Classificationstore) {
                $result = array_merge_recursive($result, $this->getClassificationStoreFields($field));
            } else {
                $result['fields']['childs'][] = $this->getFieldConfiguration($field);
            }
        }

        return $this->viewHandler->handle($result);
    }

    /**
     * @return array
     */
    protected function getSystemFields()
    {
        return [
            'systemfields' => [
                'nodeLabel' => 'system',
                'nodeType' => 'system',
                'childs' => [
                    [
                        'name' => 'id',
                        'fieldtype' => 'numeric',
                        'title' => 'ID',
                        'tooltip' => 'ID',
                    ],
                    [
                        'name' => 'key',
                        'fieldtype' => 'input',
                        'title' => 'Key',
                        'tooltip' => 'Key',
                    ],
                    [
                        'name' => 'path',
                        'fieldtype' => 'input',
                        'title' => 'Path',
                        'tooltip' => 'Path',
                    ],
                    [
                        'name' => 'creationDate',
                        'fieldtype' => 'datetime',
                        'title' => 'Creation Date',
                        'tooltip' => 'Creation Date',
                    ],
                    [
                        'name' => 'modificationDate',
                        'fieldtype' => 'datetime',
                        'title' => 'Modification Date',
                        'tooltip' => 'Modification Date',
                    ]
                ]
            ]
        ];
    }

    /**
     * @param DataObject\ClassDefinition\Data\Localizedfields $field
     * @return array
     */
    protected function getLocalizedFields(DataObject\ClassDefinition\Data\Localizedfields $field)
    {
        $result['localizedfields'] = [
            'nodeLabel' => 'localizedfields',
            'nodeType' => 'localizedfields',
            'childs' => [],
        ];

        $localizedFields = $field->getFieldDefinitions();

        foreach ($localizedFields as $localizedField) {
            $result['localizedfields']['childs'][] = $this->getFieldConfiguration($localizedField);
        }

        return $result;
    }

    /**
     * @param DataObject\ClassDefinition\Data\Objectbricks $field
     * @param DataObject\ClassDefinition $class
     * @return array
     */
    protected function getObjectbrickFields(DataObject\ClassDefinition\Data\Objectbricks $field, DataObject\ClassDefinition $class)
    {
        $result = [];

        $list = new DataObject\Objectbrick\Definition\Listing();
        $list = $list->load();

        foreach ($list as $brickDefinition) {
            if ($brickDefinition instanceof DataObject\Objectbrick\Definition) {
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

        return $result;
    }

    /**
     * @param DataObject\ClassDefinition\Data\Fieldcollections $field
     * @return array
     */
    protected function getFieldcollectionFields(DataObject\ClassDefinition\Data\Fieldcollections $field)
    {
        $result = [];

        $allowedTypes = $field->getAllowedTypes();

        if (is_array($allowedTypes)) {
            foreach ($allowedTypes as $type) {
                $definition = DataObject\Fieldcollection\Definition::getByKey($type);

                $fieldDefinition = $definition->getFieldDefinitions();

                $key = $definition->getKey();

                $result[$key] = [];
                $result[$key]['nodeLabel'] = $key;
                $result[$key]['className'] = $key;
                $result[$key]['nodeType'] = 'fieldcollections';
                $result[$key]['childs'] = [];

                foreach ($fieldDefinition as $fieldcollectionField) {
                    $result[$key]['childs'][] = $this->getFieldConfiguration($fieldcollectionField);
                }
            }
        }

        return $result;
    }

    /**
     * @param DataObject\ClassDefinition\Data\Classificationstore $field
     * @return array
     */
    protected function getClassificationStoreFields(DataObject\ClassDefinition\Data\Classificationstore $field)
    {
        $result = [];

        $list = new DataObject\Classificationstore\GroupConfig\Listing();
        $list->load();

        $allowedGroupIds = $field->getAllowedGroupIds();

        if ($allowedGroupIds) {
            $list->setCondition('ID in (' . implode(',', $allowedGroupIds) . ')');
        }

        $groupConfigList = $list->getList();

        /**
         * @var $config DataObject\Classificationstore\GroupConfig
         */
        foreach ($groupConfigList as $config) {
            $key = $config->getId() . ($config->getName() ? $config->getName() : 'EMPTY');

            $result[$key] = $this->getClassificationStoreGroupConfiguration($config);
        }

        return $result;
    }

    /**
     * @param DataObject\Classificationstore\GroupConfig $config
     *
     * @return array
     */
    protected function getClassificationStoreGroupConfiguration(DataObject\Classificationstore\GroupConfig $config)
    {
        $result = [];
        $result['nodeLabel'] = $config->getName();
        $result['nodeType'] = 'classificationstore';
        $result['childs'] = [];

        foreach ($config->getRelations() as $relation) {
            if ($relation instanceof DataObject\Classificationstore\KeyGroupRelation) {
                $keyId = $relation->getKeyId();

                $keyConfig = DataObject\Classificationstore\KeyConfig::getById($keyId);

                $result['childs'][] = $this->getClassificationStoreFieldConfiguration($keyConfig, $config);
            }
        }

        return $result;
    }

    /**
     * @param DataObject\ClassDefinition\Data $field
     *
     * @return array
     */
    protected function getFieldConfiguration(DataObject\ClassDefinition\Data $field)
    {
        return [
            'name' => $field->getName(),
            'fieldtype' => $field->getFieldtype(),
            'title' => $field->getTitle(),
            'tooltip' => $field->getTooltip(),
        ];
    }

    /**
     * @param DataObject\Classificationstore\KeyConfig $field
     * @param DataObject\Classificationstore\GroupConfig $groupConfig
     *
     * @return array
     */
    protected function getClassificationStoreFieldConfiguration(DataObject\Classificationstore\KeyConfig $field, DataObject\Classificationstore\GroupConfig $groupConfig)
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
     * @return array
     */
    protected function getInterpreterTypes()
    {
        return $this->getParameter('coreshop.index.interpreters');
    }

    /**
     * @return array
     */
    protected function getGetterTypes()
    {
        return $this->getParameter('coreshop.index.getters');
    }

    /**
     * @return array
     */
    protected function getWorkerTypes()
    {
        return $this->getParameter('coreshop.index.workers');
    }
}
