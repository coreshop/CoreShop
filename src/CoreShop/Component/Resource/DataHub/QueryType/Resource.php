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

declare(strict_types=1);

namespace CoreShop\Component\Resource\DataHub\QueryType;

use CoreShop\Component\Resource\DataHub\DoctrineProvider;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator\Input;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class Resource extends Input
{
    protected DoctrineProvider $doctrineProvider;
    protected string $className;

    public function __construct(Service $graphQlService, DoctrineProvider $doctrineProvider, string $className)
    {
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
            $container
            );
    }

    public function getFieldType(Data $fieldDefinition, $class = null, $container = null)
    {
        return $this->doctrineProvider->getGraphQlType($this->className);
    }
}
