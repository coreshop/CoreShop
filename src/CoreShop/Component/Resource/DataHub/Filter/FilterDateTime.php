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

class FilterDateTime
{
    const NAME = 'filterdatetime';

    public static function getType($dateType, $dateBetweenType)
    {

        $filterFields = array(
            array(
                'name' => 'equals',
                'type' => $dateType,
            ),
            array(
                'name' => 'greater',
                'type' => $dateType,
            ),
            array(
                'name' => 'less',
                'type' => $dateType,
            ),
            array(
                'name' => 'greaterOrEquals',
                'type' => $dateType,
            ),
            array(
                'name' => 'lessOrEquals',
                'type' => $dateType,
            ),
            array(
                'name' => 'between',
                'type' => $dateBetweenType,
            ),

        );

        return new InputObjectType(array('name' => self::NAME, 'fields' => $filterFields));
    }
}
