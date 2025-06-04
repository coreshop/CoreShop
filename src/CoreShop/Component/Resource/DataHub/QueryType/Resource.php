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

namespace CoreShop\Component\Resource\DataHub\QueryType;

use CoreShop\Component\Resource\DataHub\DoctrineProvider;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator\Input;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class Resource extends Input
{
    protected DoctrineProvider $doctrineProvider;

    protected string $className;

    public function __construct(
        Service $graphQlService,
        DoctrineProvider $doctrineProvider,
        string $className,
    ) {
        parent::__construct($graphQlService);

        $this->doctrineProvider = $doctrineProvider;
        $this->className = $className;
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
        return $this->doctrineProvider->getGraphQlType($this->className);
    }
}
