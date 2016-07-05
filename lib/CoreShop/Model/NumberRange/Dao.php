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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\NumberRange;

use CoreShop\Model\Dao\AbstractDao;

/**
 * Class Dao
 * @package CoreShop\Model\NumberRange
 */
class Dao extends AbstractDao
{
    /**
     * Mysql table name.
     * 
     * @var string
     */
    protected $tableName = 'coreshop_numberranges';

    /**
     * Get by type.
     *
     * @param null $type
     *
     * @throws \Exception
     */
    public function getByType($type = null)
    {
        $data = $this->db->fetchRow('SELECT * FROM '.$this->getTableName().' WHERE type = ?', $type);

        if (!$data['id']) {
            throw new \Exception('Object with the type '.$type." doesn't exists");
        }

        $this->assignVariablesToModel($data);
    }
}
