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

class FilterNumber
{
    const NAME = 'filternumber';

    public static function getType($dataType)
    {
        $name = self::NAME.$dataType->name;

        $filterFields = array(
            array(
                'name' => 'in',
                'type' => Type::listOf($dataType),
            ),
            array(
                'name' => 'equals',
                'type' => $dataType,
            ),
            array(
                'name' => 'greater',
                'type' => $dataType,
            ),
            array(
                'name' => 'less',
                'type' => $dataType,
            ),
            array(
                'name' => 'greaterOrEquals',
                'type' => $dataType,
            ),
            array(
                'name' => 'lessOrEquals',
                'type' => $dataType,
            ),
        );

        return new InputObjectType(array('name' => $name, 'fields' => $filterFields));
    }
}
