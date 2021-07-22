<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Resource\DataHub;

use CoreShop\Component\Resource\DataHub\Type\ArrayType;
use CoreShop\Component\Resource\DataHub\Resolver\DoctrineField;
use CoreShop\Component\Resource\DataHub\Resolver\DoctrineToMany;
use CoreShop\Component\Resource\DataHub\Resolver\DoctrineToOne;
use CoreShop\Component\Resource\DataHub\Type\JsonType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class DoctrineProvider
{
    public const JSON = 'Json';
    public const ARRAY = 'Array';

    /** @var Type[] */
    private static $standardTypes;

    /**
     * @var array
     */
    public $doctrineMetadata = array();

    /**
     * @var array
     */
    private $types = array();

    /**
     * @var array
     */
    private $typeClass = array();
    /**
     * @var array
     */
    private $doctrineToName = array();

    /**
     * @var array
     */
    private $inputTypes = array();

    /**
     * @var array
     */
    private $inputTypesToName = array();

    /**
     * @var array
     */
    private $identifierFields = array();

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var array
     */
    private $dataBuffers = array();

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;

        /**
         * @var ClassMetadataInfo $metaType
         */
        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $metaType) {
            // Ignore superclasses as they cannot be instantiated so always ignore;
            if ($metaType->isMappedSuperclass) {
                continue;
            }

            $this->initializeObjectType($metaType);
        }
    }

    public function initializeObjectType(ClassMetadataInfo $entityMetaType)
    {
        $config = array();
        $doctrineClass = $entityMetaType->getName();
        $name = $this->getGraphName($entityMetaType);

        // Setup some core data
        $this->doctrineMetadata[$name] = $entityMetaType;
        $this->typeClass[$name] = $doctrineClass;

        $this->storeTypeName($doctrineClass, $name);

        if (isset($this->types[$name])) {
            return;
        }

        $config['name'] = $name;
        $this->identifierFields[$name] = $entityMetaType->getIdentifier();
        $fields = array();

        $inputFields = array();

        foreach ($entityMetaType->getFieldNames() as $fieldName) {
            $fieldType = $this->mapFieldType($entityMetaType->getTypeOfField($fieldName));

            $resolver = new DoctrineField($fieldName, $fieldType);
            $fields[$fieldName] = $resolver->getDefinition();
            $inputFields[$fieldName] = array(
                'name' => $fieldName,
                'type' => $fieldType,
            );
        }

        $config['fields'] = function () use ($entityMetaType, $fields) {
            foreach ($entityMetaType->getAssociationMappings() as $association) {
                $fieldName = $association['fieldName'];
                $doctrineClass = $association['targetEntity'];
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

                        $inputFields[$fieldName] = array(
                            'name' => $fieldName,
                            'type' => $fieldType,
                        );

                        continue;
                    }

                    if ($association['type'] === ClassMetadataInfo::ONE_TO_MANY || $association['type'] === ClassMetadataInfo::MANY_TO_MANY) {
                        $fieldName = $association['fieldName'];
                        $fieldType = $this->getInputType($this->getTypeName($association['targetEntity']));

                        $inputFields[$fieldName] = array(
                            'name' => $fieldName,
                            'type' => Type::listOf($fieldType),
                        );
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
    }

    private function getGraphName(ClassMetadataInfo $entityMetaType)
    {
        $doctrineClass = $entityMetaType->getName();

        return str_replace('\\', '__', $doctrineClass);
    }

    public function storeTypeName($className, $name)
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

    private function hasSubClasses($entityMetaType)
    {
        return !(count($entityMetaType->subClasses) === 0);
    }

    public function getGraphQlType($className)
    {
        return $this->getType($this->getGraphName($this->em->getClassMetadata($className)));
    }

    public function getType($typeName)
    {
        if (isset($this->types[$typeName])) {
            return $this->types[$typeName];
        }

        return null;
    }

    private function hasParentClasses($entityMetaType)
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

    public function addType($typeName, $type)
    {
        $this->types[$typeName] = $type;
    }

    public function getTypes()
    {
        return $this->types;
    }

    public function getTypeKeys()
    {
        return array_keys($this->types);
    }

    public function getInputTypeKey($inputName)
    {
        return $this->inputTypesToName[$inputName];
    }

    public function getTypeIdentifiers($typeName)
    {
        return $this->identifierFields[$typeName];
    }

    public function initBuffer($bufferType, $key)
    {
        if (!isset($this->dataBuffers[$key])) {
            $this->dataBuffers[$key] = new $bufferType();
        }

        return $this->dataBuffers[$key];
    }

    public function getDoctrineType($type)
    {
        return $this->doctrineMetadata[$type];
    }

    public function getManager()
    {
        return $this->em;
    }

    public function getRepository($class)
    {
        return $this->em->getRepository($class);
    }

    public function clearBuffers()
    {
        $this->dataBuffers = array();
    }

    public function getTypeClass($graphName)
    {
        return $this->typeClass[$graphName];
    }
}
