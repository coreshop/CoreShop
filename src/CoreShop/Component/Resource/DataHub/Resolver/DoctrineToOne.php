<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Resource\DataHub\Resolver;

use CoreShop\Component\Resource\DataHub\DoctrineProvider;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DoctrineToOne
{
    public function __construct(private DoctrineProvider $typeProvider, private string $name, private string $graphName)
    {
    }

    /**
     * Generate the definition for the GraphQL field.
     */
    public function getDefinition(): array
    {
        $outputType = $this->typeProvider->getType($this->graphName);

        $args = [];

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

                return $propertyAccessor->getValue($value, $this->name);
            },
        ];
    }
}
