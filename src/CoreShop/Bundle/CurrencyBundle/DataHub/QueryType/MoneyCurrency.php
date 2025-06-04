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

namespace CoreShop\Bundle\CurrencyBundle\DataHub\QueryType;

use CoreShop\Component\Currency\Model\Money;
use CoreShop\Component\Resource\DataHub\DoctrineProvider;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator\Input;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class MoneyCurrency extends Input
{
    protected DoctrineProvider $doctrineProvider;

    protected string $currencyClass;

    public function __construct(
        Service $graphQlService,
        DoctrineProvider $doctrineProvider,
        string $currencyClass,
    ) {
        parent::__construct($graphQlService);

        $this->doctrineProvider = $doctrineProvider;
        $this->currencyClass = $currencyClass;
    }

    public function getGraphQlFieldConfig($attribute, Data $fieldDefinition, $class = null, $container = null)
    {
        return $this->enrichConfig(
            $fieldDefinition,
            $class,
            $attribute,
            [
            'name' => $fieldDefinition->getName(),
            'type' => $this->getFieldType($fieldDefinition, $class, $container),
            'resolve' => $this->getResolver($attribute, $fieldDefinition, $class),
        ],
            $container,
        );
    }

    public function getFieldType(Data $fieldDefinition, $class = null, $container = null)
    {
        return new ObjectType([
            'name' => str_replace('\\', '__', Money::class),
            'fields' => [
                [
                    'name' => 'value',
                    'type' => Type::int(),
                ],
                [
                    'name' => 'currency',
                    'type' => $this->doctrineProvider->getGraphQlType($this->currencyClass),
                ],
            ],
        ]);
    }
}
