<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Resource\DataHub\Resolver;

use CoreShop\Component\Resource\DataHub\DoctrineProvider;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DoctrineToMany
{
    private string $name;
    private string $graphName;
    private DoctrineProvider $typeProvider;

    public function __construct(
        DoctrineProvider $provider,
        string $name,
        string $graphName
    ) {
        $this->name = $name;
        $this->graphName = $graphName;
        $this->typeProvider = $provider;
    }

    public function getDefinition(): array
    {
        $args = array();

        $outputType = $this->getOutputType();

        // Create and return the definition array
        return array(
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
        );
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
        $resultFields = array();

        $resultFields[] = array(
            'name' => 'total',
            'type' => Type::int(),
        );

        $resultFields[] = array(
            'name' => 'items',
            'type' => Type::listOf($listType),
        );

        return new ObjectType(array('name' => $name, 'fields' => $resultFields));
    }
}
