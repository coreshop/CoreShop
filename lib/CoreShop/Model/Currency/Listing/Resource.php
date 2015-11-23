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

namespace CoreShop\Model\Currency\Listing;

use Pimcore\Model\Listing;
use CoreShop\Model;

class Resource extends Listing\Resource\AbstractResource {


    /**
     * Get the assets from database
     *
     * @return array
     */
    public function load() {

        $currencies = array();
        $currenciesData = $this->db->fetchAll("SELECT id FROM coreshop_currency" . $this->getCondition() . $this->getOrder() . $this->getOffsetLimit(), $this->model->getConditionVariables());

        foreach ($currenciesData as $currencyData) {
            if($currency = Model\Currency::getById($currencyData["id"])) {
                $currencies[] = $currency;
            }
        }

        $this->model->setCurrencies($currencies);
        return $currencies;
    }

    /**
     * Loads a list of document ids for the specicifies parameters, returns an array of ids
     *
     * @return array
     */
    public function loadIdList() {
        $currencyIds = $this->db->fetchCol("SELECT id FROM coreshop_currency" . $this->getCondition() . $this->getOrder() . $this->getOffsetLimit(), $this->model->getConditionVariables());
        return $currencyIds;
    }

    public function getCount() {
        $amount = (int) $this->db->fetchOne("SELECT COUNT(*) as amount FROM coreshop_currency" . $this->getCondition() . $this->getOffsetLimit(), $this->model->getConditionVariables());
        return $amount;
    }

    public function getTotalCount() {
        $amount = (int) $this->db->fetchOne("SELECT COUNT(*) as amount FROM coreshop_currency" . $this->getCondition(), $this->model->getConditionVariables());
        return $amount;
    }
}