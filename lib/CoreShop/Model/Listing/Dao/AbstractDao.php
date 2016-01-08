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
use Pimcore\Tool;

class AbstractDao extends Listing\Dao\AbstractDao {

    protected $tableName = '';

    protected $modelClass;

    protected function getTableName () {
        if(!$this->model->getIgnoreLocalizedFields())
        {
            $language = null;

            $model = new $this->modelClass();

            if(count($model->getLocalizedFields()) > 0) {
                if($this->model->getLocale()) {
                    if(Tool::isValidLanguage((string) $this->model->getLocale())) {
                        $language = (string) $this->model->getLocale();
                    }
                }

                if(!$language && \Zend_Registry::isRegistered("Zend_Locale")) {
                    $locale = \Zend_Registry::get("Zend_Locale");
                    if(Tool::isValidLanguage((string) $locale)) {
                        $language = (string) $locale;
                    }
                }

                if (!$language) {
                    $language = Tool::getDefaultLanguage();
                }

                if (!$language) {
                    throw new \Exception("No valid language/locale set. Use \$list->setLocale() to add a language to the listing, or register a global locale");
                }

                $this->tableName = $this->tableName . "_data_localized_" . $language;
            }
        }

        return $this->tableName;
    }

    /**
     * Get the assets from database
     *
     * @return array
     */
    public function load() {
        $modelClass = $this->modelClass;

        $data = array();
        $rawData = $this->db->fetchAll("SELECT id FROM " . $this->getTableName() . $this->getCondition() . $this->getOrder() . $this->getOffsetLimit(), $this->model->getConditionVariables());

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
        $currencyIds = $this->db->fetchCol("SELECT id FROM " . $this->getTableName() . $this->getCondition() . $this->getOrder() . $this->getOffsetLimit(), $this->model->getConditionVariables());
        return $currencyIds;
    }

    public function getCount() {
        $amount = (int) $this->db->fetchOne("SELECT COUNT(*) as amount FROM " . $this->getTableName() . $this->getCondition() . $this->getOffsetLimit(), $this->model->getConditionVariables());
        return $amount;
    }

    public function getTotalCount() {
        $amount = (int) $this->db->fetchOne("SELECT COUNT(*) as amount FROM " . $this->getTableName() . $this->getCondition(), $this->model->getConditionVariables());
        return $amount;
    }
}