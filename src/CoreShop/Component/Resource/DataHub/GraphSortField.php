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

namespace CoreShop\Component\Resource\DataHub;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class GraphSortField
{
    const NAME = 'SortField';

    public $field;
    public $order;

    /**
     * @return InputObjectType
     */
    public static function getType()
    {
        $typeFields = array(
            array(
                'name' => 'field',
                'type' => Type::string(),
            ),
            array(
                'name' => 'order',
                'type' => Type::string(),
            ),
        );

        return new InputObjectType(array('name' => self::NAME, 'fields' => $typeFields));
    }
}
