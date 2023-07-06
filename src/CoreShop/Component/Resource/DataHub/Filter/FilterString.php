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

namespace CoreShop\Component\Resource\DataHub\Filter;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class FilterString
{
    const NAME = 'filterstring';

    public static function getType()
    {
        $filterFields = array(
            array(
                'name' => 'contains',
                'type' => Type::string(),
            ),
            array(
                'name' => 'equals',
                'type' => Type::string(),
            ),
            array(
                'name' => 'startsWith',
                'type' => Type::string(),
            ),
            array(
                'name' => 'endsWith',
                'type' => Type::string(),
            ),
            array(
                'name' => 'in',
                'type' => Type::listOf(Type::string()),
            ),
        );

        return new InputObjectType(array('name' => self::NAME, 'fields' => $filterFields));
    }
}
