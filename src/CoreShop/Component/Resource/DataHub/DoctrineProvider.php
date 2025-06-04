<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Resource\DataHub;

use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Resource\DataHub\Filter\FilterDateTime;
use CoreShop\Component\Resource\DataHub\Filter\FilterDateTimeBetween;
use CoreShop\Component\Resource\DataHub\Filter\FilterNumber;
use CoreShop\Component\Resource\DataHub\Filter\FilterString;
use CoreShop\Component\Resource\DataHub\Resolver\DoctrineField;
use CoreShop\Component\Resource\DataHub\Resolver\DoctrineToMany;
use CoreShop\Component\Resource\DataHub\Resolver\DoctrineToOne;
use CoreShop\Component\Resource\DataHub\Type\ArrayType;
use CoreShop\Component\Resource\DataHub\Type\BigIntType;
use CoreShop\Component\Resource\DataHub\Type\DateTimeType;
use CoreShop\Component\Resource\DataHub\Type\JsonType;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Metadata\RegistryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\IntType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\StringType;
use GraphQL\Type\Definition\Type;
use PHPStan\Type\BooleanType;

class DoctrineProvider
{
    public const JSON = 'Json';

    public const ARRAY = 'Array';

    /** @var Type[] */
    private static array|null $standardTypes = null;

    private array $doctrineMetadata = [];

    private array $types = [];

    private array $typeClass = [];

    private array $doctrineToName = [];

    private array $inputTypes = [];

    private array $inputTypesToName = [];

    private array $inputQueryFilterTypes = [];

    private array $identifierFields = [];

    private array $classMap = [];

    private $niceNameMap = [];

    public function __construct(
        private EntityManagerInterface $em,
        private RegistryInterface $metadataRegistry,
    ) {
        $this->types[GraphPageInfo::NAME] = GraphPageInfo::getType();
        $this->types[GraphSortField::NAME] = GraphSortField::getType();
        $this->types[FilterString::NAME] = FilterString::getType();
        $this->types[FilterNumber::NAME . '_int'] = FilterNumber::getType(Type::int());
        $this->types[FilterDateTimeBetween::NAME] = FilterDateTimeBetween::getType($this->getType('datetime'));
        $this->types[FilterDateTime::NAME] = FilterDateTime::getType($this->getType('datetime'), $this->getType(FilterDateTimeBetween::NAME));

        $this->initialize();
    }

    public function initialize()
    {
        /**
         * @var MetadataInterface $metadata
         */
        foreach ($this->metadataRegistry->getAll() as $metadata) {
            if ($metadata->getDriver() !== CoreShopResourceBundle::DRIVER_DOCTRINE_ORM) {
                continue;
            }

            if (!$metadata->hasParameter('graphql')) {
                continue;
            }

            if (!$metadata->getParameter('graphql')['enabled'] ?? false) {
                continue;
            }

            $className = $metadata->getClass('model');
            $this->classMap[$className] = $metadata;
        }

        foreach ($this->classMap as $className => $metadata) {
            $className = $metadata->getClass('model');
            $niceName = ucfirst($metadata->getApplicationName()) . ucfirst(str_replace('_', '', ucwords($metadata->getName(), '_')));

            $this->initializeClass($className, $niceName);
        }
    }

    public function initializeClass(string $className, string $niceName): void
    {
        $this->initializeResource($this->em->getClassMetadata($className), $niceName);
    }

    public function initializeResource(ClassMetadataInfo $entityMetaType, string $niceName): void
    {
        $config = [];
        $doctrineClass = $entityMetaType->getName();

        $class = $entityMetaType->getReflectionClass();
        $className = $this->getGraphName($entityMetaType);
        $name = $niceName;

        $this->niceNameMap[$className] = $niceName;

        // Setup some core data
        $this->doctrineMetadata[$name] = $entityMetaType;
        $this->typeClass[$name] = $doctrineClass;

        $queryFilterFields = GraphPageInfo::getQueryFilters($this);

        $this->storeTypeName($doctrineClass, $name);

        if (isset($this->types[$name])) {
            return;
        }

        $config['name'] = $name;
        $this->identifierFields[$name] = $entityMetaType->getIdentifier();
        $fields = [];

        $inputFields = [];

        foreach ($entityMetaType->getFieldNames() as $fieldName) {
            $fieldType = $this->mapFieldType($entityMetaType->getTypeOfField($fieldName));

            $resolver = new DoctrineField($fieldName, $fieldType);
            $fields[$fieldName] = $resolver->getDefinition();
            $inputFields[$fieldName] = [
                'name' => $fieldName,
                'type' => $fieldType,
            ];

            $filterFields[$fieldName] = [
                'name' => $fieldName,
                'type' => Type::listOf($fieldType),
            ];

            // Define the top level query filters
            if ($fieldType instanceof StringType) {
                $queryFilterFields[$fieldName] = [
                    'name' => $fieldName,
                    'type' => $this->getType(FilterString::NAME),
                ];
            } elseif ($fieldType instanceof DateTimeType) {
                $queryFilterFields[$fieldName] = [
                    'name' => $fieldName,
                    'type' => $this->getType(FilterDateTime::NAME),
                ];
            } elseif ($fieldType instanceof BigIntType) {
                $queryFilterFields[$fieldName] = [
                    'name' => $fieldName,
                    'type' => $this->getType(FilterNumber::NAME . '_bigint'),
                ];
            } elseif ($fieldType instanceof IntType) {
                $queryFilterFields[$fieldName] = [
                    'name' => $fieldName,
                    'type' => $this->getType(FilterNumber::NAME . '_int'),
                ];
            } else {
                $queryFilterFields[$fieldName] = [
                    'name' => $fieldName,
                    'type' => Type::listOf($fieldType),
                ];
            }

            // Define the input properties
            $inputFields[$fieldName] = [
                'name' => $fieldName,
                'type' => $fieldType,
            ];
        }

        $config['fields'] = function () use ($entityMetaType, $fields) {
            foreach ($entityMetaType->getAssociationMappings() as $association) {
                $fieldName = $association['fieldName'];
                $doctrineClass = $association['targetEntity'];

                if (!array_key_exists($doctrineClass, $this->classMap)) {
                    continue;
                }

                $graphName = $this->getTypeName($doctrineClass);

                if (isset($this->doctrineMetadata[$graphName])) {
                    $resolver = null;

                    if ($association['type'] === ClassMetadataInfo::ONE_TO_ONE || $association['type'] === ClassMetadataInfo::MANY_TO_ONE) {
                        $resolver = new DoctrineToOne($this, $fieldName, $graphName);
                    } else {
                        $resolver = new DoctrineToMany($this, $fieldName, $graphName);
                    }

                    $fields[$fieldName] = $resolver->getDefinition();
                }
            }

            return $fields;
        };

        $interfaces = [];

        if ($this->hasSubClasses($entityMetaType)) {
            $interfaceConfig = $config;
            $interfaceKey = $name . '__Interface';
            $interfaceConfig['name'] .= '__Interface';
            $interfaceConfig['resolveType'] = function ($value) use ($entityMetaType) {
                $column = $entityMetaType->discriminatorColumn['fieldName'];
                $type = $value->getDataValue($column);
                $instanceType = $entityMetaType->discriminatorMap[$type];

                return $this->getType($this->getTypeName($instanceType));
            };

            $this->types[$interfaceKey] = new InterfaceType($interfaceConfig);

            $interfaces[] = $this->getType($interfaceKey);

            $config['interfaces'] = $interfaces;
        }

        // If this class has parent classes then we want to add the parent classes
        if ($this->hasParentClasses($entityMetaType)) {
            foreach ($entityMetaType->parentClasses as $parent) {
                $parentName = $this->getTypeName($parent);

                $interfaces[] = $this->getType($parentName . '__Interface');
            }

            if (count($interfaces) > 0) {
                $config['interfaces'] = $interfaces;
            }
        }

        $this->types[$name] = new ObjectType($config);

        $inputConfig = [
            'name' => $config['name'] . '__Input',
            'fields' => function () use ($entityMetaType, $inputFields) {
                foreach ($entityMetaType->getAssociationMappings() as $association) {
                    if ($association['type'] === ClassMetadataInfo::MANY_TO_ONE || $association['type'] === ClassMetadataInfo::ONE_TO_ONE) {
                        $fieldName = $association['fieldName'];
                        $fieldType = $this->getInputType($this->getTypeName($association['targetEntity']));

                        $inputFields[$fieldName] = [
                            'name' => $fieldName,
                            'type' => $fieldType,
                        ];

                        continue;
                    }

                    if ($association['type'] === ClassMetadataInfo::ONE_TO_MANY || $association['type'] === ClassMetadataInfo::MANY_TO_MANY) {
                        $fieldName = $association['fieldName'];
                        $fieldType = $this->getInputType($this->getTypeName($association['targetEntity']));

                        $inputFields[$fieldName] = [
                            'name' => $fieldName,
                            'type' => Type::listOf($fieldType),
                        ];
                    }
                }

                return $inputFields;
            },
        ];

        // Instantiate the input type
        if (count($inputFields) > 0) {
            $this->inputTypes[$name] = new InputObjectType($inputConfig);
            $this->inputTypesToName[$inputConfig['name']] = $name;
        }

        $inputQueryFilterConfig = [
            'name' => $config['name'] . '__QueryFilter',
            'fields' => function () use ($entityMetaType, $queryFilterFields, $class) {
                foreach ($entityMetaType->getAssociationMappings() as $association) {
                    if ($association['type'] === ClassMetadataInfo::MANY_TO_ONE || $association['type'] === ClassMetadataInfo::ONE_TO_ONE) {
                        $fieldName = $association['fieldName'];
                        $fieldType = $this->getInputType($this->getTypeName($association['targetEntity']));

                        // Define the input properties
                        $queryFilterFields[$fieldName] = [
                            'name' => $fieldName,
                            'type' => $fieldType,
                        ];
                    }
                }

                return $queryFilterFields;
            },
        ];

        // Instantiate the query filter type
        if (count($queryFilterFields) > 0) {
            $this->inputQueryFilterTypes[$name] = new InputObjectType($inputQueryFilterConfig);
        }
    }

    private function getGraphName(ClassMetadataInfo $entityMetaType): string
    {
        $doctrineClass = $entityMetaType->getName();

        return str_replace('\\', '__', $doctrineClass);
    }

    private function getNiceName(ClassMetadataInfo $entityMetaType): ?string
    {
        return $this->niceNameMap[$this->getGraphName($entityMetaType)] ?? null;
    }

    public function storeTypeName($className, $name): void
    {
        $key = str_replace('\\', '__', $className);

        $this->doctrineToName[$key] = $name;
    }

    private static function getStandardType($name = null)
    {
        if (self::$standardTypes === null) {
            self::$standardTypes = [
                self::JSON => new JsonType(),
                self::ARRAY => new ArrayType(),
            ];
        }

        return $name ? self::$standardTypes[$name] : self::$standardTypes;
    }

    private function mapFieldType($doctrineType)
    {
        switch ($doctrineType) {
            case 'integer':
            case 'bigint':
            case 'smallint':
                return Type::int();
            case 'boolean':
                return Type::boolean();
            case 'float':
            case 'decimal':
                return Type::float();
            case 'json_array':
                return self::getStandardType(self::JSON);
            case 'array':
            case 'json':
                return self::getStandardType(self::ARRAY);
        }

        // Default to string
        return Type::string();
    }

    public function getTypeName($className)
    {
        $key = str_replace('\\', '__', $className);

        return $this->doctrineToName[$key];
    }

    private function hasSubClasses($entityMetaType): bool
    {
        return !(count($entityMetaType->subClasses) === 0);
    }

    public function getGraphQlType($className)
    {
        return $this->getType($this->getNiceName($this->em->getClassMetadata($className)));
    }

    public function getGraphQlQueries(): array
    {
        $queries = [];

        foreach ($this->metadataRegistry->getAll() as $metadata) {
            if ($metadata->getDriver() !== CoreShopResourceBundle::DRIVER_DOCTRINE_ORM) {
                continue;
            }

            $className = $metadata->getClass('model');
            $niceName = ucfirst($metadata->getApplicationName()) . ucfirst(str_replace('_', '', ucwords($metadata->getName(), '_')));

            $queries[$niceName . '__List'] = $this->getGraphQlQuery($niceName, $className);
        }

        return $queries;
    }

    public function getGraphQlQuery($niceName, $className): array
    {
        $graphType = $this->getGraphQlType($className);
        $inputType = $this->getQueryFilterType($graphType->name);
        $args = [];

        if ($inputType !== null) {
            foreach ($inputType->getFields() as $field) {
                $args[$field->name] = ['name' => $field->name, 'type' => $field->getType()];
            }
        }

        $pageInfoType = $this->getType(GraphPageInfo::NAME);
        $outputTypeName = $niceName . '__List';

        if ($this->getType($outputTypeName) === null) {
            $outputType = GraphResultList::getType($outputTypeName, $graphType, $pageInfoType);

            $this->addType($outputTypeName, $outputType);
        } else {
            $outputType = $this->getType($outputTypeName);
        }

        return [
            'name' => $outputTypeName,
            'type' => $outputType,
            'args' => $args,
            'resolve' => function ($root, $args, $context, $info) use ($className, $graphType) {
                $em = $this->em;

                $qb = $em->getRepository($className)->createQueryBuilder('e');
                $inputType = $this->getQueryFilterType($graphType->name);
                $identifiers = $this->getTypeIdentifiers($graphType->name);

                // Add the appropriate DQL clauses required for pagination based
                // on the supplied args. Args get removed once used.
                $filteredArgs = GraphPageInfo::paginateQuery($qb, $identifiers, $args);
                $filteredArgs = GraphPageInfo::sortQuery($qb, $identifiers, $filteredArgs);

                $joinCount = 0;

                foreach ($filteredArgs as $name => $values) {
                    $fields = $inputType->getFields();
                    $fieldType = $fields[$name]->getType();

                    if ($fieldType instanceof JsonType) {
                        foreach ($values as $filter) {
                            foreach ($filter as $path => $valueInfo) {
                                $value = $valueInfo['value'];
                                $valueType = $valueInfo['type'];

                                if ($valueType === 'text') {
                                    $value = '\'' . $value . '\'';
                                }

                                //$qb->andWhere("CAST(e." . $name . ", 'mortgage', 'boolean') = true");
                                $qb->andWhere('JSON_PATH_EQUALS(e.' . $name . ", '" . $path . "', '" . $valueType . "') = " . $value);
                            }
                        }
                    } elseif ($fieldType instanceof InputObjectType) {
                        if ($fieldType->name === FilterString::NAME) {
                            if (isset($values['in'])) {
                                $qb->andWhere($qb->expr()->in('e.' . $name, ':' . $name));
                                $qb->setParameter($name, $values['in']);
                            } elseif (isset($values['equals'])) {
                                $qb->andWhere($qb->expr()->eq('e.' . $name, ':' . $name));
                                $qb->setParameter($name, $values['equals']);
                            } elseif (isset($values['startsWith'])) {
                                $qb->andWhere($qb->expr()->like('e.' . $name, ':' . $name));
                                $qb->setParameter($name, $values['startsWith'] . '%');
                            } elseif (isset($values['endsWith'])) {
                                $qb->andWhere($qb->expr()->like('e.' . $name, ':' . $name));
                                $qb->setParameter($name, '%' . $values['endsWith']);
                            } elseif (isset($values['contains'])) {
                                $qb->andWhere($qb->expr()->like('e.' . $name, ':' . $name));
                                $qb->setParameter($name, '%' . $values['contains'] . '%');
                            }
                        } elseif ($fieldType->name === FilterDateTime::NAME) {
                            if (isset($values['equals'])) {
                                $qb->andWhere($qb->expr()->eq('e.' . $name, ':' . $name));
                                $qb->setParameter($name, $values['equals']);
                            } elseif (isset($values['greater'])) {
                                $qb->andWhere('e.' . $name . ' > :' . $name);
                                $qb->setParameter($name, $values['greater']);
                            } elseif (isset($values['less'])) {
                                $qb->andWhere('e.' . $name . ' < :' . $name);
                                $qb->setParameter($name, $values['less']);
                            } elseif (isset($values['greaterOrEquals'])) {
                                $qb->andWhere('e.' . $name . ' >= :' . $name);
                                $qb->setParameter($name, $values['greaterOrEquals']);
                            } elseif (isset($values['lessOrEquals'])) {
                                $qb->andWhere('e.' . $name . ' <= :' . $name);
                                $qb->setParameter($name, $values['lessOrEquals']);
                            } elseif (isset($values['between'], $values['between']['from'], $values['between']['to'])) {
                                $qb->andWhere('e.' . $name . ' BETWEEN :from AND :to');
                                $qb->setParameter('from', $values['between']['from']);
                                $qb->setParameter('to', $values['between']['to']);
                            }
                        } elseif (strpos($fieldType->name, FilterNumber::NAME) === 0) {
                            if (isset($values['in'])) {
                                $qb->andWhere($qb->expr()->in('e.' . $name, ':' . $name));
                                $qb->setParameter($name, $values['in']);
                            } elseif (isset($values['equals'])) {
                                $qb->andWhere($qb->expr()->eq('e.' . $name, ':' . $name));
                                $qb->setParameter($name, $values['equals']);
                            } elseif (isset($values['greater'])) {
                                $qb->andWhere('e.' . $name . ' > :' . $name);
                                $qb->setParameter($name, $values['greater']);
                            } elseif (isset($values['less'])) {
                                $qb->andWhere('e.' . $name . ' < :' . $name);
                                $qb->setParameter($name, $values['less']);
                            } elseif (isset($values['greaterOrEquals'])) {
                                $qb->andWhere('e.' . $name . ' >= :' . $name);
                                $qb->setParameter($name, $values['greaterOrEquals']);
                            } elseif (isset($values['lessOrEquals'])) {
                                $qb->andWhere('e.' . $name . ' <= :' . $name);
                                $qb->setParameter($name, $values['lessOrEquals']);
                            }
                        } else {
                            $typeClass = $this->getTypeClass($this->getInputTypeKey($fieldType->name));

                            if ($typeClass !== null) {
                                $alias = 'e' . $joinCount;
                                $qb->addSelect($alias)->leftJoin('e.' . $name, $alias);

                                foreach ($values as $associatedField => $associatedValue) {
                                    $qb->andWhere(
                                        $qb->expr()->eq(
                                            $alias . '.' . $associatedField,
                                            ':' . $associatedField,
                                        ),
                                    );
                                    $qb->setParameter($associatedField, $associatedValue);
                                }

                                ++$joinCount;
                            }
                        }
                    } else {
                        if ($fieldType instanceof ListOfType && $fieldType->ofType instanceof BooleanType) {
                            $updatedValues = [];

                            foreach ($values as $value) {
                                $updatedValues[] = ($value !== true ? 'false' : 'true');
                            }

                            $values = $updatedValues;
                        }

                        $qb->andWhere($qb->expr()->in('e.' . $name, ':' . $name));
                        $qb->setParameter($name, $values);
                    }
                }

                $query = $qb->getQuery();
                $query->setHint('doctrine.includeMetaColumns', true);

                $dataList = $query->getResult();

                return new GraphResultList(
                    $dataList,
                    $args,
                );
            },
        ];
    }

    public function getType($typeName)
    {
        if (isset($this->types[$typeName])) {
            return $this->types[$typeName];
        }

        return null;
    }

    public function getQueryFilterType($typeName)
    {
        if (isset($this->inputQueryFilterTypes[$typeName])) {
            return $this->inputQueryFilterTypes[$typeName];
        }

        return null;
    }

    public function getRepository($class)
    {
        return $this->em->getRepository($class);
    }

    private function hasParentClasses($entityMetaType): bool
    {
        return !(count($entityMetaType->parentClasses) === 0);
    }

    public function getInputType($typeName)
    {
        if (isset($this->inputTypes[$typeName])) {
            return $this->inputTypes[$typeName];
        }

        return null;
    }

    public function addType($typeName, $type): void
    {
        $this->types[$typeName] = $type;
    }

    public function getTypes()
    {
        return $this->types;
    }

    public function getInputTypeKey($inputName)
    {
        return $this->inputTypesToName[$inputName];
    }

    public function getTypeIdentifiers($typeName)
    {
        return $this->identifierFields[$typeName];
    }

    public function getTypeClass($graphName)
    {
        return $this->typeClass[$graphName];
    }
}
