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

namespace CoreShop\Model\Listing\Resource;

use Pimcore\Model\Listing;
use CoreShop\Model;

class AbstractResource extends Listing\Resource\AbstractResource {

    protected $tableName = '';

    protected $modelClass;

    /**
     * Get the assets from database
     *
     * @return array
     */
    public function load() {
        $modelClass = $this->modelClass;

        $data = array();
        $rawData = $this->db->fetchAll("SELECT id FROM " . $this->tableName . $this->getCondition() . $this->getOrder() . $this->getOffsetLimit(), $this->model->getConditionVariables());

        foreach ($rawData as $raw) {

            if($object = $modelClass::getById($raw["id"])) {
                $data[] = $object;
            }
        }

        $this->model->setData($data);
        return $data;
    }

    /**
     * Loads a list for the specicifies parameters, returns an array of ids
     *
     * @return array
     */
    public function loadIdList() {
        $currencyIds = $this->db->fetchCol("SELECT id FROM " . $this->tableName . $this->getCondition() . $this->getOrder() . $this->getOffsetLimit(), $this->model->getConditionVariables());
        return $currencyIds;
    }

    public function getCount() {
        $amount = (int) $this->db->fetchOne("SELECT COUNT(*) as amount FROM " . $this->tableName . $this->getCondition() . $this->getOffsetLimit(), $this->model->getConditionVariables());
        return $amount;
    }

    public function getTotalCount() {
        $amount = (int) $this->db->fetchOne("SELECT COUNT(*) as amount FROM " . $this->tableName . $this->getCondition(), $this->model->getConditionVariables());
        return $amount;
    }
}