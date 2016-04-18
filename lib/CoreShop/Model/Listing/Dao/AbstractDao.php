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

class AbstractDao extends Listing\Dao\AbstractDao
{
    /**
     * @var bool
     */
    protected $firstException = true;

    /**
     * @var string
     */
    protected $tableName = '';

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * Get tableName, either for localized or non-localized data
     *
     * @return string
     * @throws \Exception
     * @throws \Zend_Exception
     */
    protected function getTableName()
    {
        if (!$this->model->getIgnoreLocalizedFields()) {
            $language = null;

            $model = new $this->modelClass();

            if (count($model->getLocalizedFields()) > 0) {
                if ($this->model->getLocale()) {
                    if (Tool::isValidLanguage((string) $this->model->getLocale())) {
                        $language = (string) $this->model->getLocale();
                    }
                }

                if (!$language && \Zend_Registry::isRegistered("Zend_Locale")) {
                    $locale = \Zend_Registry::get("Zend_Locale");
                    if (Tool::isValidLanguage((string) $locale)) {
                        $language = (string) $locale;
                    }
                }

                if (!$language) {
                    $language = Tool::getDefaultLanguage();
                }

                if (!$language) {
                    throw new \Exception("No valid language/locale set. Use \$list->setLocale() to add a language to the listing, or register a global locale");
                }

                return $this->tableName . "_data_localized_" . $language;
            }
        }

        return $this->tableName;
    }

    /**
     * get select query
     *
     * @return \Zend_Db_Select
     * @throws \Exception
     */
    public function getQuery()
    {

        // init
        $select = $this->db->select();

        // create base
        $field = $this->getTableName() . ".id";
        $select->from(
            [ $this->getTableName() ], [
                new \Zend_Db_Expr(sprintf('SQL_CALC_FOUND_ROWS %s as id', $field, 'o_type'))
            ]
        );

        // add condition
        $this->addConditions($select);

        // group by
        $this->addGroupBy($select);

        // order
        $this->addOrder($select);

        // limit
        $this->addLimit($select);

        return $select;
    }

    /**
     * Loads objects from the database
     *
     * @return Model\AbstractModel[]
     */
    public function load()
    {
        $modelClass = $this->modelClass;

        // load id's
        $list = $this->loadIdList();


        $objects = array();
        foreach ($list as $o_id) {
            if ($object = $modelClass::getById($o_id)) {
                $objects[] = $object;
            }
        }

        $this->model->setData($objects);
        return $objects;
    }

    /**
     * Loads a list for the specicifies parameters, returns an array of ids
     *
     * @return array
     */
    public function loadIdList()
    {
        try {
            $query = $this->getQuery();
            $objectIds = $this->db->fetchCol($query, $this->model->getConditionVariables());
            $this->totalCount = (int)$this->db->fetchOne('SELECT FOUND_ROWS()');

            return $objectIds;
        } catch (\Exception $e) {
            return $this->exceptionHandler($e);
        }
    }

    /**
     * Handles Exceptions
     *
     * @param $e
     * @return array
     * @throws
     * @throws \Exception
     */
    protected function exceptionHandler($e)
    {

        // create view if it doesn't exist already // HACK
        $pdoMySQL = preg_match("/Base table or view not found/", $e->getMessage());
        $Mysqli = preg_match("/Table (.*) doesn't exist/", $e->getMessage());

        if (($Mysqli || $pdoMySQL) && $this->firstException) {
            $this->firstException = false;

            $modelClass = $this->modelClass;
            $model = new $modelClass();

            $localizedFields = $model->getLocalizedFields();

            if ($localizedFields) {
                $localizedFields->createUpdateTable();
            }

            return $this->loadIdList();
        }

        throw $e;
    }

    /**
     * Get Count
     *
     * @return int
     * @throws \Exception
     */
    public function getCount()
    {
        $amount = (int) $this->db->fetchOne("SELECT COUNT(*) as amount FROM " . $this->getTableName() . $this->getCondition() . $this->getOffsetLimit(), $this->model->getConditionVariables());
        return $amount;
    }

    /**
     * Get Total Count
     *
     * @return int
     * @throws \Exception
     */
    public function getTotalCount()
    {
        $amount = (int) $this->db->fetchOne("SELECT COUNT(*) as amount FROM " . $this->getTableName() . $this->getCondition(), $this->model->getConditionVariables());
        return $amount;
    }
}
