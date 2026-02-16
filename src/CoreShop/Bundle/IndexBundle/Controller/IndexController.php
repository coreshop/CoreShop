<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\IndexBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\RuleFormSchemaCollector;
use CoreShop\Component\Index\Interpreter\LocalizedInterpreterInterface;
use CoreShop\Component\Index\Interpreter\RelationInterpreterInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Pimcore\Model\DataObject;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\SubscribedService;

class IndexController extends ResourceController
{
    public function getTypesAction(): Response
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

    public function getConfigAction(
        RuleFormSchemaCollector $schemaCollector,
        #[Autowire(service: 'coreshop.form_registry.index.getter')]
        FormTypeRegistryInterface $getterFormTypeRegistry,
        #[Autowire(service: 'coreshop.form_registry.index.interpreter')]
        FormTypeRegistryInterface $interpreterFormTypeRegistry,
        #[Autowire(service: 'coreshop.form_registry.index.worker')]
        FormTypeRegistryInterface $workerFormTypeRegistry,
    ): Response {
        $indexInterpreterRegistry = $this->container->get('coreshop.registry.index.interpreter');

        $getterTypes = $this->getGetterTypes();
        $interpreterTypes = $this->getInterpreterTypes();
        $workerTypes = $this->getWorkerTypes();

        $getterSchemas = $schemaCollector->collectSchemasWithTypeMap($getterFormTypeRegistry, $getterTypes);
        $interpreterSchemas = $schemaCollector->collectSchemasWithTypeMap($interpreterFormTypeRegistry, $interpreterTypes);
        $workerSchemas = $schemaCollector->collectSchemasWithTypeMap($workerFormTypeRegistry, $workerTypes);

        $gettersResult = [];

        foreach ($getterTypes as $getter) {
            $entry = [
                'type' => $getter,
                'name' => $getter,
            ];

            if (isset($getterSchemas['schemaByType'][$getter])) {
                $entry['blockPrefix'] = $getterSchemas['schemaByType'][$getter];
            }

            $gettersResult[] = $entry;
        }

        $interpretersResult = [];

        foreach ($interpreterTypes as $interpreter) {
            $class = $indexInterpreterRegistry->get($interpreter);
            $implements = class_implements($class) ?: [];

            $entry = [
                'type' => $interpreter,
                'name' => $interpreter,
                'localized' => in_array(LocalizedInterpreterInterface::class, $implements, true),
                'relation' => in_array(RelationInterpreterInterface::class, $implements, true),
            ];

            if (isset($interpreterSchemas['schemaByType'][$interpreter])) {
                $entry['blockPrefix'] = $interpreterSchemas['schemaByType'][$interpreter];
            }

            $interpretersResult[] = $entry;
        }

        /**
         * @var array $workerFieldTypes
         */
        $workerFieldTypes = $this->getParameter('coreshop.index.worker_mapping_types');
        $fieldTypesResult = [];

        foreach ($workerFieldTypes as $workerType => $fieldTypes) {
            $fieldTypesResult[$workerType] = [];

            foreach ($fieldTypes as $type => $mapping) {
                $fieldTypesResult[$workerType][] = [
                    'type' => $type,
                    'name' => ucfirst(strtolower($type)),
                ];
            }
        }

        $classes = new DataObject\ClassDefinition\Listing();
        $classes = $classes->load();
        $availableClasses = [];

        foreach ($classes as $class) {
            if ($class instanceof DataObject\ClassDefinition) {
                $pimcoreClass = 'Pimcore\Model\DataObject\\' . ucfirst($class->getName());
                $implements = class_implements($pimcoreClass) ?: [];

                if (in_array(IndexableInterface::class, $implements, true)) {
                    $availableClasses[] = [
                        'name' => $class->getName(),
                    ];
                }
            }
        }

        $workersResult = [];

        foreach ($workerTypes as $workerType) {
            $entry = [
                'type' => $workerType,
                'name' => $workerType,
            ];

            if (isset($workerSchemas['schemaByType'][$workerType])) {
                $entry['blockPrefix'] = $workerSchemas['schemaByType'][$workerType];
            }

            $workersResult[] = $entry;
        }

        return $this->viewHandler->handle(
            [
                'success' => true,
                'interpreters' => $interpretersResult,
                'getters' => $gettersResult,
                'fieldTypes' => $fieldTypesResult,
                'classes' => $availableClasses,
                'workerTypes' => $workerTypes,
                'workers' => $workersResult,
                'schemas' => array_merge(
                    $getterSchemas['schemas'],
                    $interpreterSchemas['schemas'],
                    $workerSchemas['schemas'],
                ),
                'getterSchemaByType' => $getterSchemas['schemaByType'],
                'interpreterSchemaByType' => $interpreterSchemas['schemaByType'],
                'workerSchemaByType' => $workerSchemas['schemaByType'],
            ],
        );
    }

    public function getClassDefinitionForFieldSelectionAction(Request $request): Response
    {
        $class = DataObject\ClassDefinition::getByName($this->getParameterFromRequest($request, 'class'));

        if (!$class instanceof DataObject\ClassDefinition) {
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

        $result = array_merge_recursive($result, $this->getSystemFields());

        $allowedBricks = [];
        $allowedCollections = [];

        foreach ($fields as $field) {
            if ($field instanceof DataObject\ClassDefinition\Data\Localizedfields) {
                $result = array_merge_recursive($result, $this->getLocalizedFields($field));
            } elseif ($field instanceof DataObject\ClassDefinition\Data\Objectbricks) {
                foreach ($field->getAllowedTypes() as $type) {
                    if (!in_array($type, $allowedBricks)) {
                        $allowedBricks[] = $type;
                    }
                }
            } elseif ($field instanceof DataObject\ClassDefinition\Data\Fieldcollections) {
                foreach ($field->getAllowedTypes() as $type) {
                    if (!in_array($type, $allowedCollections)) {
                        $allowedCollections[] = $type;
                    }
                }
            } elseif ($field instanceof DataObject\ClassDefinition\Data\Classificationstore) {
                $result = array_merge_recursive($result, $this->getClassificationStoreFields($field));
            } else {
                $result['fields']['childs'][] = $this->getFieldConfiguration($field);
            }
        }

        $this->getObjectbrickFields($allowedBricks, $result);
        $this->getFieldcollectionFields($allowedCollections, $result);

        return $this->viewHandler->handle($result);
    }

    public function getOpenSearchClientsAction(): Response
    {
        $clientRegistry = $this->container->get('coreshop.registry.index.opensearch_client');

        $result = [
            'clients' => \array_map(
                static fn (string $client) => ['name' => $client],
                \array_keys($clientRegistry->all()),
            ),
        ];

        return $this->viewHandler->handle($result);
    }

    protected function getSystemFields(): array
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
                    ],
                ],
            ],
        ];
    }

    protected function getLocalizedFields(DataObject\ClassDefinition\Data\Localizedfields $field): array
    {
        $result = [
            'localizedfields' => [
                'nodeLabel' => 'localizedfields',
                'nodeType' => 'localizedfields',
                'childs' => [],
            ],
        ];

        $localizedFields = $field->getFieldDefinitions();

        foreach ($localizedFields as $localizedField) {
            $result['localizedfields']['childs'][] = $this->getFieldConfiguration($localizedField);
        }

        return $result;
    }

    protected function getObjectbrickFields(array $allowedBricks, &$result): array
    {
        foreach ($allowedBricks as $brickKey) {
            $brickDefinition = DataObject\Objectbrick\Definition::getByKey($brickKey);

            if ($brickDefinition instanceof DataObject\Objectbrick\Definition) {
                $key = $brickDefinition->getKey();
                $fields = $brickDefinition->getFieldDefinitions();

                $result[$key] = [];
                $result[$key]['nodeLabel'] = $key;
                $result[$key]['nodeType'] = 'objectbricks';
                $result[$key]['childs'] = [];

                foreach ($fields as $field) {
                    if ($field instanceof DataObject\ClassDefinition\Data\Localizedfields) {
                        $localizedfields = $this->getLocalizedFields($field)['localizedfields'];

                        foreach ($localizedfields['childs'] as &$child) {
                            $child['getter'] = 'brick';
                            $child['configuration'] = [
                                'className' => $key,
                                'key' => $field->getName(),
                            ];
                        }

                        $result[$key]['childs'][] = $localizedfields;
                    } else {
                        $fieldConfig = $this->getFieldConfiguration($field);

                        $fieldConfig['getter'] = 'brick';
                        $fieldConfig['configuration'] = [
                            'className' => $key,
                            'key' => $field->getName(),
                        ];
                        $result[$key]['childs'][] = $fieldConfig;
                    }
                }
            }
        }

        return $result;
    }

    protected function getFieldcollectionFields(array $allowedCollections, &$result): array
    {
        foreach ($allowedCollections as $type) {
            $definition = DataObject\Fieldcollection\Definition::getByKey($type);

            $fieldDefinition = $definition->getFieldDefinitions();

            $key = $definition->getKey();

            $result[$key] = [];
            $result[$key]['nodeLabel'] = $key;
            $result[$key]['nodeType'] = 'fieldcollections';
            $result[$key]['childs'] = [];

            foreach ($fieldDefinition as $fieldcollectionField) {
                if ($fieldcollectionField instanceof DataObject\ClassDefinition\Data\Localizedfields) {
                    $localizedfields = $this->getLocalizedFields($fieldcollectionField)['localizedfields'];

                    foreach ($localizedfields['childs'] as &$child) {
                        $child['getter'] = 'fieldcollection';
                        $child['configuration'] = [
                            'className' => $key,
                        ];
                    }

                    $result[$key]['childs'][] = $localizedfields;
                } else {
                    $fieldConfig = $this->getFieldConfiguration($fieldcollectionField);

                    $fieldConfig['getter'] = 'fieldcollection';
                    $fieldConfig['configuration'] = [
                        'className' => $key,
                    ];
                    $result[$key]['childs'][] = $fieldConfig;
                }
            }
        }

        return $result;
    }

    protected function getClassificationStoreFields(DataObject\ClassDefinition\Data\Classificationstore $field): array
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
         * @var DataObject\Classificationstore\GroupConfig $config
         */
        foreach ($groupConfigList as $config) {
            $key = $config->getId() . ($config->getName() ?: 'EMPTY');

            $result[$key] = $this->getClassificationStoreGroupConfiguration($config);
        }

        return $result;
    }

    protected function getClassificationStoreGroupConfiguration(
        DataObject\Classificationstore\GroupConfig $config,
    ): array {
        $result = [];
        $result['nodeLabel'] = $config->getName();
        $result['nodeType'] = 'classificationstore';
        $result['childs'] = [];

        $relations = $config->getRelations();

        foreach ($relations as $relation) {
            if ($relation instanceof DataObject\Classificationstore\KeyGroupRelation) {
                $keyId = $relation->getKeyId();

                $keyConfig = DataObject\Classificationstore\KeyConfig::getById($keyId);

                $result['childs'][] = $this->getClassificationStoreFieldConfiguration($keyConfig, $config);
            }
        }

        return $result;
    }

    protected function getFieldConfiguration(DataObject\ClassDefinition\Data $field): array
    {
        $definition = [
            'name' => $field->getName(),
            'fieldtype' => $field->getFieldtype(),
            'title' => $field->getTitle(),
            'tooltip' => $field->getTooltip(),
        ];

        if ($field instanceof DataObject\ClassDefinition\Data\QuantityValue) {
            $definition['interpreter'] = 'quantityValue';
        }

        return $definition;
    }

    protected function getClassificationStoreFieldConfiguration(
        DataObject\Classificationstore\KeyConfig $field,
        DataObject\Classificationstore\GroupConfig $groupConfig,
    ): array {
        $definition = [
            'name' => $field->getName(),
            'getter' => 'classificationstore',
            'fieldtype' => $field->getType(),
            'title' => $field->getName(),
            'tooltip' => $field->getDescription(),
            'configuration' => [
                'keyConfigId' => $field->getId(),
                'groupConfigId' => $groupConfig->getId(),
            ],
        ];

        if ('quantityValue' === $field->getType()) {
            $definition['interpreter'] = ' quantityValue';
        }

        return $definition;
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            new SubscribedService(
                'coreshop.registry.index.interpreter',
                ServiceRegistryInterface::class,
                attributes: new Autowire(service: 'coreshop.registry.index.interpreter'),
            ),
            new SubscribedService(
                'coreshop.registry.index.opensearch_client',
                ServiceRegistryInterface::class,
                attributes: new Autowire(service: 'coreshop.registry.index.opensearch_client'),
            ),
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected function getInterpreterTypes(): array
    {
        return $this->getParameter('coreshop.index.interpreters');
    }

    /**
     * @return array<string, string>
     */
    protected function getGetterTypes(): array
    {
        return $this->getParameter('coreshop.index.getters');
    }

    /**
     * @return array<string, string>
     */
    protected function getWorkerTypes(): array
    {
        return $this->getParameter('coreshop.index.workers');
    }
}
