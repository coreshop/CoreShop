<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Resource\DataHub\Resolver;

use GraphQL\Type\Definition\Type;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DoctrineField
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Type
     */
    private $type;

    /**
     * @param string $name
     * @param Type   $type
     */
    public function __construct(string $name, Type $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getDefinition()
    {
        /**
         * Value will be the parent object when it's passed in.
         */
        return array(
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
        );
    }
}
