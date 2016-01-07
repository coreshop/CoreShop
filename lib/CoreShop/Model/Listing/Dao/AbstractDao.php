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

namespace CoreShop\Model\Listing\Dao;

use Pimcore\Model\Listing;
use CoreShop\Model;

class AbstractDao extends Listing\Dao\AbstractDao {

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