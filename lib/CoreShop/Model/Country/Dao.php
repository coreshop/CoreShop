<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Country;

use CoreShop\Model\Country;
use CoreShop\Model\Dao\AbstractDao;
use Pimcore\Db;

/**
 * Class Dao
 * @package CoreShop\Model\Country
 */
class Dao extends AbstractDao
{
    /**
     * Mysql table name.
     *
     * @var string
     */
    protected static $tableName = 'coreshop_countries';

    /**
     * @param $shopId
     * @return array
     */
    public function getActiveCountries($shopId) {
        $db = Db::get();

        $data = $db->fetchAll('SELECT countries.id FROM ' . $this->getTableName() . ' as countries INNER JOIN ' . $this->getShopTableName() . ' ON oId = id AND shopId = ? WHERE active = 1', [$shopId]);
        $result = [];

        foreach($data as $entry) {
            $result[] = Country::getById($entry['id']);
        }

        return $result;
    }
}
