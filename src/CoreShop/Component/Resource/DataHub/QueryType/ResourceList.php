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

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class ResourceList extends Resource
{
    public function getFieldType(Data $fieldDefinition, $class = null, $container = null)
    {
        return Type::listOf($this->doctrineProvider->getGraphQlType($this->className));
    }

    public function getResolver($attribute, $fieldDefinition, $class)
    {
        $parentResolver = parent::getResolver($attribute, $fieldDefinition, $class);

        return function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) use (
            $parentResolver
        ) {
            $value = $parentResolver($value, $args, $context, $resolveInfo);

            return array_map(function ($id) {
                return $this->doctrineProvider->getRepository($this->className)->find($id);
            }, $value);
        };
    }
}
