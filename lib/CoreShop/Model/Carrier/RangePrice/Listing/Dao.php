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

namespace CoreShop\Model\Carrier\RangePrice\Listing;

use CoreShop\Model\Listing;
use CoreShop\Model;

class Dao extends Listing\Dao\AbstractDao
{

    protected $tableName = 'coreshop_carriers_range_price';
    protected $modelClass = '\\CoreShop\\Model\\Carrier\\RangePrice';
}
