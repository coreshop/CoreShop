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

namespace CoreShop\Component\Resource\DataHub\Resolver;

use CoreShop\Component\Resource\DataHub\DoctrineProvider;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DoctrineToMany
{
    public function __construct(
        private DoctrineProvider $typeProvider,
        private string $name,
        private string $graphName,
    ) {
    }

    public function getDefinition(): array
    {
        $args = [];

        $outputType = $this->getOutputType();

        // Create and return the definition array
        return [
            'name' => $this->name,
            'type' => $outputType,
            'args' => $args,
            'resolve' => function ($value, $args, $context, $info) {
                if (is_array($value)) {
                    return $value[$this->name];
                }

                if (method_exists($value, 'get')) {
                    return $value->get($this->name);
                }

                $propertyAccessor = new PropertyAccessor();
                $collection = $propertyAccessor->getValue($value, $this->name);

                $result = $collection->toArray();

                return ['items' => $result, 'total' => count($result)];
            },
        ];
    }

    public function getOutputType()
    {
        $listType = $this->typeProvider->getType($this->graphName);
        $outputTypeName = $listType->name . '__List';

        if ($this->typeProvider->getType($outputTypeName) === null) {
            $outputType = $this->getListType($outputTypeName, $listType);

            $this->typeProvider->addType($outputTypeName, $outputType);

            return $outputType;
        }

        return $this->typeProvider->getType($outputTypeName);
    }

    protected function getListType($name, $listType)
    {
        $resultFields = [];

        $resultFields[] = [
            'name' => 'total',
            'type' => Type::int(),
        ];

        $resultFields[] = [
            'name' => 'items',
            'type' => Type::listOf($listType),
        ];

        return new ObjectType(['name' => $name, 'fields' => $resultFields]);
    }
}
