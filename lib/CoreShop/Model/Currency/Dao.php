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

namespace CoreShop\Model\Currency;

use CoreShop\Model\Dao\AbstractDao;

class Dao extends AbstractDao {

    protected $tableName = 'coreshop_currencies';

    /**
     * @param null $name
     * @throws \Exception
     */
    public function getByName($name = null) {

        $data = $this->db->fetchRow('SELECT * FROM '.$this->getTableName().' WHERE name = ?', $name);

        if(!$data["id"])
            throw new \Exception("Object with the name " . $name . " doesn't exists");

        $this->assignVariablesToModel($data);
    }
}