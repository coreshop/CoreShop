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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Configuration;

use CoreShop\Exception;
use Pimcore\Model;
use Pimcore\Tool;

/**
 * Class Dao
 * @package CoreShop\Model\Configuration
 */
class Dao extends Model\Dao\PhpArrayTable
{
    /**
     * Configure Configuration File.
     */
    public function configure()
    {
        parent::configure();
        $this->setFile('coreshop_configurations');
    }

    /**
     * Get Configuration By Id.
     *
     * @param null $id
     *
     * @throws Exception
     */
    public function getById($id = null)
    {
        if ($id != null) {
            $this->model->setId($id);
        }

        $data = $this->db->getById($this->model->getId());

        if (isset($data['id'])) {
            $this->assignVariablesToModel($data);
        } else {
            throw new Exception('Configuration with id: '.$this->model->getId().' does not exist');
        }
    }

    /**
     * Get Configuration by key.
     *
     * @param null $key
     * @param null $shopId
     *
     * @throws Exception
     */
    public function getByKey($key = null, $shopId = null)
    {
        if ($key != null) {
            $this->model->setKey($key);
        }

        $key = $this->model->getKey();

        $data = $this->db->fetchAll(function ($row) use ($key, $shopId) {
            if($shopId) {
                if ($row['key'] == $key && $row['shopId'] === intval($shopId)) {
                    return true;
                }
            }
            else {
                if ($row['key'] == $key && $row['shopId'] === null) {
                    return true;
                }
            }

            return false;
        });

        //Fallback for Inherited Configurations
        if($shopId) {
            if (!count($data)) {
                $data = $this->db->fetchAll(function ($row) use ($key, $shopId) {
                    if ($row['key'] == $key && $row['shopId'] === null) {
                        return true;
                    }

                    return false;
                });
            }
        }

        if (count($data) && $data[0]['id']) {
            $this->assignVariablesToModel($data[0]);
        } else {
            throw new Exception('Configuration with key: '.$this->model->getKey().' does not exist');
        }
    }

    /**
     * save configuration.
     *
     * @throws \Exception
     */
    public function save()
    {
        $ts = time();
        if (!$this->model->getCreationDate()) {
            $this->model->setCreationDate($ts);
        }
        $this->model->setModificationDate($ts);

        try {
            $dataRaw = get_object_vars($this->model);
            $data = [];
            $allowedProperties = ['id', 'shopId', 'key', 'data', 'creationDate', 'modificationDate'];

            foreach ($dataRaw as $key => $value) {
                if (in_array($key, $allowedProperties)) {
                    $data[$key] = $value;
                }
            }
            $this->db->insertOrUpdate($data, $this->model->getId());
        } catch (\Exception $e) {
            throw $e;
        }

        if (!$this->model->getId()) {
            $this->model->setId($this->db->getLastInsertId());
        }
    }

    public function removeAll($key) {
        $data = $this->db->fetchAll();

        foreach($data as $d) {
            $d->delete();
        }
    }

    /**
     * Deletes object from database.
     */
    public function delete()
    {
        $this->db->delete($this->model->getId());
    }
}
