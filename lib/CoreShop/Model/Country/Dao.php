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

namespace CoreShop\Model\Country;

use CoreShop\Model\Dao\AbstractDao;

class Dao extends AbstractDao {

    protected $tableName = 'coreshop_countries';

    public function getByIsoCode($isoCode = null)
    {
        $data = $this->db->fetchRow('SELECT * FROM '.$this->tableName.' WHERE isoCode = ?', $isoCode);

        if(!$data["id"])
            throw new \Exception("Object with the isoCode " . $isoCode . " doesn't exists");

        $this->assignVariablesToModel($data);
    }
}