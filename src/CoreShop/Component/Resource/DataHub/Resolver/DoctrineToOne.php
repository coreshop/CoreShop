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

namespace CoreShop\Component\Resource\DataHub\Resolver;

use CoreShop\Component\Resource\DataHub\DoctrineProvider;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DoctrineToOne
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $graphName;

    /**
     * @var DoctrineProvider
     */
    private $typeProvider;

    public function __construct(
        DoctrineProvider $provider,
        string $name,
        string $graphName
    ) {
        $this->name = $name;
        $this->graphName = $graphName;
        $this->typeProvider = $provider;
    }

    /**
     * Generate the definition for the GraphQL field.
     *
     * @return array
     */
    public function getDefinition()
    {
        $outputType = $this->typeProvider->getType($this->graphName);

        $args = array();

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

                return $propertyAccessor->getValue($value, $this->name);
            },
        );
    }
}
