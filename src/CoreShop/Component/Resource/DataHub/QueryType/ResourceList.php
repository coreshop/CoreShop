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

namespace CoreShop\Component\Resource\DataHub\QueryType;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class ResourceList extends Resource
{
    /**
     * {@inheritdoc}
     */
    public function getFieldType(Data $fieldDefinition, $class = null, $container = null)
    {
        return Type::listOf($this->doctrineProvider->getGraphQlType($this->className));
    }

    public function getResolver($attribute, $fieldDefinition, $class)
    {
        $parentResolver = parent::getResolver($attribute, $fieldDefinition, $class);

        return function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) use ($parentResolver) {
            $value = $parentResolver($value, $args, $context, $resolveInfo);

            return array_map(function ($id) {
                return $this->doctrineProvider->getRepository($this->className)->find($id);
            }, $value);
        };
    }
}
