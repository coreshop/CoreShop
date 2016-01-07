<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShop\Model\NumberRange;

use CoreShop\Model\Dao\AbstractDao;

class Dao extends AbstractDao {

    protected $tableName = 'coreshop_numberRanges';

    public function getByType($type = null)
    {
        $data = $this->db->fetchRow('SELECT * FROM '.$this->tableName.' WHERE type = ?', $type);

        if(!$data["id"])
            throw new \Exception("Object with the type " . $type . " doesn't exists");

        $this->assignVariablesToModel($data);
    }
}