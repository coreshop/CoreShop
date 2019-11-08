<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
    /**
     * @var DoctrineProvider
     */
    protected $doctrineProvider;

    /**
     * @var string
     */
    protected $currencyClass;

    public function __construct(Service $graphQlService, DoctrineProvider $doctrineProvider, string $currencyClass)
    {
        parent::__construct($graphQlService);

        $this->doctrineProvider = $doctrineProvider;
        $this->currencyClass = $currencyClass;
    }

    /**
     * {@inheritdoc}
     */
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
            $container
            );
    }

    /**
     * {@inheritdoc}
     */
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
