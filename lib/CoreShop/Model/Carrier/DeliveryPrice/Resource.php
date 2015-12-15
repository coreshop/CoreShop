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

namespace CoreShop\Model\Carrier\DeliveryPrice;

use CoreShop\Model\Resource\AbstractResource;

class Resource extends AbstractResource {

    protected $tableName = 'coreshop_carriers_delivery_price';

    public function getByCarrierAndRange($carrier, $range)
    {
        $data = $this->db->fetchRow('SELECT * FROM '.$this->tableName.' WHERE `carrier` = ? AND `range` = ?', [$carrier, $range]);

        if(!$data["id"])
            throw new \Exception(get_class($this->model) . " with the ID " . $this->model->getId() . " doesn't exists");

        $this->assignVariablesToModel($data);
    }

    public function getForCarrierInZone($carrier, $range, $zone) {
        $data = $this->db->fetchRow('SELECT * FROM '.$this->tableName.' WHERE `carrierId` = ? AND `rangeId` = ? AND zoneId = ?', [$carrier, $range, $zone]);

        if(!$data["id"])
            throw new \Exception(get_class($this->model) . " with the ID " . $this->model->getId() . " doesn't exists");

        $this->assignVariablesToModel($data);
    }
}