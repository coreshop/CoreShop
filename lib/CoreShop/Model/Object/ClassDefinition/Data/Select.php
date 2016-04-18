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

namespace CoreShop\Model\Object\ClassDefinition\Data;

use CoreShop\Model\AbstractModel;
use Pimcore\Model;
use CoreShop\Model\Country;

class Select extends Model\Object\ClassDefinition\Data\Select
{
    
    /**
     * Type for the column to query
     *
     * @var string
     */
    public $queryColumnType = "int(11)";

    /**
     * Type for the column
     *
     * @var string
     */
    public $columnType = "int(11)";


    /** True if change is allowed in edit mode.
     * @return bool
     */
    public function isDiffChangeAllowed()
    {
        return true;
    }

    /**
     * get data for resource
     *
     * @see Object\ClassDefinition\Data::getDataForResource
     * @param AbstractModel $data
     * @param null|Model\Object\AbstractObject $object
     * @return integer|null
     */
    public function getDataForResource($data, $object = null)
    {
        if (is_a($data, $this->getPhpdocType())) {
            return $data->getId();
        }
        return null;
    }

    /**
     * get data from resource
     *
     * @see Object\ClassDefinition\Data::getDataFromResource
     * @param integer $data
     * @return AbstractModel
     */
    public function getDataFromResource($data)
    {
        if (intval($data) > 0) {
            return call_user_func($this->getPhpdocType() . '::getById', $data);
        }
        return null;
    }

    /**
     * get data for query resource
     *
     * @see Object\ClassDefinition\Data::getDataForQueryResource
     * @param AbstractModel $data
     * @param null|Model\Object\AbstractObject $object
     * @return integer|null
     */
    public function getDataForQueryResource($data, $object = null)
    {
        if (is_a($data, $this->getPhpdocType())) {
            return $data->getId();
        }
        return null;
    }

    /**
     * get data for editmode
     *
     * @see Object\ClassDefinition\Data::getDataForEditmode
     * @param AbstractModel $data
     * @param null|Model\Object\AbstractObject $object
     * @param $objectFromVersion
     * @return integer
     */
    public function getDataForEditmode($data, $object = null, $objectFromVersion = null)
    {
        return $this->getDataForResource($data, $object, $objectFromVersion);
    }

    /**
     * @see Model\Object\ClassDefinition\Data::getDataFromEditmode
     * @param integer $data
     * @param null|Model\Object\AbstractObject $object
     * @param array $params
     * @return AbstractModel
     */
    public function getDataFromEditmode($data, $object = null, $params = array())
    {
        return $this->getDataFromResource($data, $object, $params);
    }

    /**
     * is empty
     *
     * @param Model\Object\Concrete $data
     * @return bool
     */
    public function isEmpty($data)
    {
        return !$data;
    }

    /**
     * get data for search index
     *
     * @param $object
     * @return int|string
     */
    public function getDataForSearchIndex($object)
    {
        if ($object instanceof Model\Object\AbstractObject) {
            return $object->getId();
        }

        return parent::getDataForSearchIndex($object);
    }
}
