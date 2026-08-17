<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\ResourceBundle\DataHub\Resolver;

use CoreShop\Component\Resource\Model\ResourceInterface;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class ResourceResolver
{
    public function __construct(
        public Data $fieldDefinition,
    ) {
    }

    public function resolve($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $resource = $value[$this->fieldDefinition->getName()];

        if ($resource instanceof ResourceInterface) {
            return $resource->getId();
        }

        return null;
    }
}
