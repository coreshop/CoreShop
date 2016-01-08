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

namespace CoreShop\Model\Carrier\DeliveryPrice;

use CoreShop\Model\Dao\AbstractDao;

class Dao extends AbstractDao {

    protected $tableName = 'coreshop_carriers_delivery_price';

    public function getByCarrierAndRange($carrier, $range)
    {
        $data = $this->db->fetchRow('SELECT * FROM '.$this->getTableName().' WHERE `carrier` = ? AND `range` = ?', [$carrier, $range]);

        if(!$data["id"])
            throw new \Exception(get_class($this->model) . " with the ID " . $this->model->getId() . " doesn't exists");

        $this->assignVariablesToModel($data);
    }

    public function getForCarrierInZone($carrier, $range, $zone) {
        $data = $this->db->fetchRow('SELECT * FROM '.$this->getTableName().' WHERE `carrierId` = ? AND `rangeId` = ? AND zoneId = ?', [$carrier, $range, $zone]);

        if(!$data["id"])
            throw new \Exception(get_class($this->model) . " with the ID " . $this->model->getId() . " doesn't exists");

        $this->assignVariablesToModel($data);
    }
}