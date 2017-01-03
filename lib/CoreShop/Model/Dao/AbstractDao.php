<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Dao;

use Carbon\Carbon;
use CoreShop\Exception;
use CoreShop\Model\AbstractModel;
use CoreShop\Model\Configuration;
use CoreShop\Model\Shop;
use Pimcore\Date;
use Pimcore\Model\Object\AbstractObject;
use Pimcore\Model\Dao;
use Pimcore\Tool\Serialize;

/**
 * Class AbstractDao
 * @package CoreShop\Model\Dao
 */
abstract class AbstractDao extends Dao\AbstractDao
{
    /**
     * @var string
     */
    protected static $tableName = '';

    /**
     * Get table name.
     *
     * @return string
     */
    public function getTableName()
    {
        $class = get_called_class();

        if (\Pimcore::getDiContainer()->has($class)) {
            $class = \Pimcore::getDiContainer()->get($class);
        }

        return $class::$tableName;
    }

    /**
     * Get Shop table name
     *
     * @return string
     */
    public function getShopTableName()
    {
        return $this->getTableName() . '_shops';
    }

    /**
     * Get Object by id.
     *
     * @param null $id
     * @param null $shopId
     *
     * @throws Exception
     */
    public function getById($id = null, $shopId = null)
    {
        if ($id != null) {
            $this->model->setId($id);
        }

        $data = null;

        if (!is_null($shopId) && $this->model->isMultiShop()) {
            $data = $this->db->fetchRow('SELECT * FROM ' . $this->getTableName() . ' INNER JOIN ' . $this->getShopTableName() . ' ON oId = id AND shopId = ? WHERE id = ?', [$shopId, $this->model->getId()]);
        } else {
            $data = $this->db->fetchRow('SELECT * FROM ' . $this->getTableName() . ' WHERE id = ?', $this->model->getId());
        }

        if (!$data['id']) {
            if (!is_null($shopId) && $this->model->isMultiShop()) {
                throw new Exception(sprintf('%s with the ID "%s" for ShopId "%s" does not exist or is not activated', get_class($this->model), $this->model->getId(), $shopId));
            } else {
                throw new Exception(sprintf('%s with the ID "%s" for does not exist', get_class($this->model), $this->model->getId()));
            }
        }

        if ($this->model->isMultiShop()) {
            $shops = $this->db->fetchAll('SELECT * FROM ' . $this->getShopTableName() . ' WHERE oId = ?', [$this->model->getId()]);
            $shopIds = [];

            if (is_array($shops)) {
                foreach ($shops as $shop) {
                    $shopIds[] = $shop['shopId'];
                }
            }

            $this->model->setShopIds($shopIds);
        }

        $this->assignVariablesToModel($data);
        $this->getData();
    }

    /**
     * Get Object by field.
     *
     * @param string $field
     * @param string $value
     * @param int $shopId
     *
     * @throws Exception
     */
    public function getByField($field, $value, $shopId = null)
    {
        $data = null;

        if (!is_null($shopId)) {
            if ($this->model->isMultiShop()) {
                $data = $this->db->fetchRow('SELECT * FROM ' . $this->getTableName() . ' INNER JOIN ' . $this->getShopTableName() . ' ON oId = id AND shopId = ? WHERE ' . $field . ' = ?', [$shopId, $value]);
            } elseif ($this->model->isMultiShopFK()) {
                $data = $this->db->fetchRow('SELECT * FROM ' . $this->getTableName() . ' WHERE shopId = ? AND ' . $field . ' = ?', [$shopId, $value]);
            }
        } else {
            $data = $this->db->fetchRow('SELECT * FROM ' . $this->getTableName() . " WHERE $field = ?", $value);
        }

        if (!$data['id']) {
            throw new Exception(get_class($this->model).' with the field/value '.$field.'-'.$value." doesn't exists");
        }

        $this->assignVariablesToModel($data);
        $this->getData();
    }

    /**
     * Get the data-elements for the object from database for the given path.
     */
    public function getData()
    {
        if ($this->model->getLocalizedFields()) {
            $this->model->getLocalizedFields()->load();
        }
    }

    /**
     * Save object.
     *
     * @throws \Zend_Db_Adapter_Exception
     */
    public function save()
    {
        $vars = get_object_vars($this->model);

        $buffer = [];

        $validColumns = $this->getValidTableColumns($this->getTableName());

        if (count($vars)) {
            foreach ($vars as $k => $v) {
                if (!in_array($k, $validColumns)) {
                    continue;
                }

                $getter = 'get'.ucfirst($k);

                if (!is_callable([$this->model, $getter])) {
                    continue;
                }

                $value = $this->model->$getter();

                if (is_bool($value)) {
                    $value = (int) $value;
                }
                if (is_array($value)) {
                    $value = Serialize::serialize($value);
                }
                if ($value instanceof AbstractObject) {
                    $value = $value->getId();
                }
                if ($value instanceof AbstractModel) {
                    $value = $value->getId();
                }
                if ($value instanceof Date || $value instanceof Carbon) {
                    $value = $value->getTimestamp();
                }
                if ($value instanceof \Zend_Date) {
                    $value = $value->getTimestamp();
                }
                if (is_object($value)) {
                    $value = Serialize::serialize($value);
                }

                $buffer[$k] = $value;
            }
        }

        if ($this->model->getId() !== null) {
            $this->db->update($this->getTableName(), $buffer, $this->db->quoteInto('id = ?', $this->model->getId()));

            if ($this->model->getLocalizedFields()) {
                $this->model->getLocalizedFields()->save();
            }

            $this->saveShopIds();

            return;
        }

        $this->db->insert($this->getTableName(), $buffer);
        $this->model->setId($this->db->lastInsertId());

        if ($this->model->getLocalizedFields()) {
            $this->model->getLocalizedFields()->save();
        }

        $this->saveShopIds();
    }

    /**
     * Delete Object.
     */
    public function delete()
    {
        $this->db->delete($this->getTableName(), $this->db->quoteInto('id = ?', $this->model->getId()));
    }

    /**
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function saveShopIds()
    {
        if ($this->model->isMultiShop()) {
            $this->db->delete($this->getShopTableName(), $this->db->quoteInto('oId = ?', $this->model->getId()));

            if (is_null($this->model->getShopIds())) {
                $this->model->setShopIds([Shop::getDefaultShop()->getId()]);
            }

            if (!Configuration::multiShopEnabled()) {
                //Multishop is disabled, so we always set the default shop
                $this->model->setShopIds([Shop::getDefaultShop()->getId()]);
            }

            foreach ($this->model->getShopIds() as $shopId) {
                $this->db->insert($this->getShopTableName(), [
                    "oId" => $this->model->getId(),
                    "shopId" => $shopId
                ]);
            }
        }
    }
}
