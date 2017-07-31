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
use Pimcore\Model\Object;
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

        $classes = new Object\ClassDefinition\Listing();
        $classes = $classes->load();
        $availableClasses = [];

        foreach ($classes as $class) {
            if ($class instanceof Object\ClassDefinition) {
                $pimcoreClass = 'Pimcore\Model\Object\\' . ucfirst($class->getName());

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
        $class = Object\ClassDefinition::getByName($request->get('class'));

        if (!$class instanceof Object\ClassDefinition) {
            return $this->viewHandler->handle([]);
        }

        $fields = $class->getFieldDefinitions();

        $result = [
            'fields' => [
                'nodeLabel' => 'fields',
                'nodeType' => 'object',
                'childs' => [],
            ],
        ];

        foreach ($fields as $field) {
            if ($field instanceof Object\ClassDefinition\Data\Localizedfields) {
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
            } elseif ($field instanceof Object\ClassDefinition\Data\Fieldcollections) {
                foreach ($field->getAllowedTypes() as $type) {
                    $definition = Object\Fieldcollection\Definition::getByKey($type);

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

        return $this->viewHandler->handle($result);
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

    /**
     * @param Object\Classificationstore\GroupConfig $config
     *
     * @return array
     */
    protected function getClassificationStoreGroupConfiguration(Object\Classificationstore\GroupConfig $config)
    {
        $result = [];
        $result['nodeLabel'] = $config->getName();
        $result['nodeType'] = 'classificationstore';
        $result['childs'] = [];

        foreach ($config->getRelations() as $relation) {
            if ($relation instanceof Object\Classificationstore\KeyGroupRelation) {
                $keyId = $relation->getKeyId();

                $keyConfig = Object\Classificationstore\KeyConfig::getById($keyId);

                $result['childs'][] = $this->getClassificationStoreFieldConfiguration($keyConfig, $config);
            }
        }

        return $result;
    }

    /**
     * @param Object\ClassDefinition\Data $field
     *
     * @return array
     */
    protected function getFieldConfiguration(Object\ClassDefinition\Data $field)
    {
        return [
            'name' => $field->getName(),
            'fieldtype' => $field->getFieldtype(),
            'title' => $field->getTitle(),
            'tooltip' => $field->getTooltip(),
        ];
    }

    /**
     * @param Object\Classificationstore\KeyConfig   $field
     * @param Object\Classificationstore\GroupConfig $groupConfig
     *
     * @return array
     */
    protected function getClassificationStoreFieldConfiguration(Object\Classificationstore\KeyConfig $field, Object\Classificationstore\GroupConfig $groupConfig)
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
