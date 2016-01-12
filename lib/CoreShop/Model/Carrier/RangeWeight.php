<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Carrier;

use CoreShop\Tool;

class RangeWeight extends AbstractRange
{
    /**
     * Get RangeWeight by id
     * @param $id
     * @return null|static
     */
    public static function getById($id) {
        return parent::getById($id, "weight");
    }
}