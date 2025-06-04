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

use GraphQL\Type\Definition\Type;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DoctrineField
{
    public function __construct(
        private string $name,
        private Type $type,
    ) {
    }

    public function getDefinition(): array
    {
        /**
         * Value will be the parent object when it's passed in.
         */
        return [
            'name' => $this->name,
            'type' => $this->type,
            'resolve' => function ($value, $args, $context, $info) {
                if (is_array($value)) {
                    return $value[$this->name];
                }

                if (method_exists($value, 'get')) {
                    return $value->get($this->name);
                }

                $propertyAccessor = new PropertyAccessor();

                return $propertyAccessor->getValue($value, $this->name);
            },
        ];
    }
}
