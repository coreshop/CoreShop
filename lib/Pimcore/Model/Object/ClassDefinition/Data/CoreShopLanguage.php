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

namespace Pimcore\Model\Object\ClassDefinition\Data;

use CoreShop\Model\AbstractModel;
use Pimcore\Model;

/**
 * Class CoreShopLanguage
 * @package Pimcore\Model\Object\ClassDefinition\Data
 */
class CoreShopLanguage extends Model\Object\ClassDefinition\Data\Select
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopLanguage';

    /**
     * Type for the column to query.
     *
     * @var string
     */
    public $queryColumnType = 'varchar(255)';

    /**
     * Type for the column.
     *
     * @var string
     */
    public $columnType = 'varchar(255)';

    /**
     * @return string
     */
    public function getColumnType()
    {
        return $this->columnType;
    }

    /**
     * @return string
     */
    public function getQueryColumnType()
    {
        return $this->queryColumnType;
    }

    /** True if change is allowed in edit mode.
     * @param string $object
     * @param mixed $params
     * @return bool
     */
    public function isDiffChangeAllowed($object, $params = [])
    {
        return true;
    }

    /**
     * @see Object\ClassDefinition\Data::getDataForResource
     * @param string $data
     * @param null|Model\Object\AbstractObject $object
     * @param mixed $params
     * @return string
     */
    public function getDataForResource($data, $object = null, $params = [])
    {
        return $data;
    }

    /**
     * @see Object\ClassDefinition\Data::getDataFromResource
     * @param string $data
     * @param null|Model\Object\AbstractObject $object
     * @param mixed $params
     * @return string
     */
    public function getDataFromResource($data, $object = null, $params = [])
    {
        return $data;
    }

    /**
     * @see Object\ClassDefinition\Data::getDataForQueryResource
     * @param string $data
     * @param null|Model\Object\AbstractObject $object
     * @param mixed $params
     * @return string
     */
    public function getDataForQueryResource($data, $object = null, $params = [])
    {
        return $data;
    }

    /**
     * get data for editmode.
     *
     * @see Object\ClassDefinition\Data::getDataForEditmode
     *
     * @param AbstractModel                    $data
     * @param null|Model\Object\AbstractObject $object
     * @param $objectFromVersion
     *
     * @return int
     */
    public function getDataForEditmode($data, $object = null, $objectFromVersion = null)
    {
        return $this->getDataForResource($data, $object);
    }

    /**
     * @see Model\Object\ClassDefinition\Data::getDataFromEditmode
     * @param string $data
     * @param null|Model\Object\AbstractObject $object
     * @param mixed $params
     * @return string
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        return $this->getDataFromResource($data, $object, $params);
    }

    /**
     * is empty.
     *
     * @param Model\Object\Concrete $data
     *
     * @return bool
     */
    public function isEmpty($data)
    {
        return !$data;
    }

    /**
     * get data for search index.
     *
     * @param $object
     * @param mixed $params
     *
     * @return int|string
     */
    public function getDataForSearchIndex($object, $params = [])
    {
        return parent::getDataForSearchIndex($object, $params);
    }
}
