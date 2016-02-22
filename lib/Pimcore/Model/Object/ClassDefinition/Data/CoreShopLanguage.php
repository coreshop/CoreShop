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

namespace Pimcore\Model\Object\ClassDefinition\Data;

use CoreShop\Model\AbstractModel;
use Pimcore\Model;
use CoreShop\Model\Country;

class CoreShopLanguage extends Model\Object\ClassDefinition\Data\Select
{
    /**
     * Static type of this element
     *
     * @var string
     */
    public $fieldtype = "coreShopLanguage";

    /**
     * Type for the column to query
     *
     * @var string
     */
    public $queryColumnType = "varchar(255)";

    /**
     * Type for the column
     *
     * @var string
     */
    public $columnType = "varchar(255)";


    /** True if change is allowed in edit mode.
     * @return bool
     */
    public function isDiffChangeAllowed()
    {
        return true;
    }

    /**
     * @see Object\ClassDefinition\Data::getDataForResource
     * @param AbstractModel $data
     * @param null|Model\Object\AbstractObject $object
     * @return integer|null
     */
    public function getDataForResource($data, $object = null)
    {
        return $data;
    }

    /**
     * @see Object\ClassDefinition\Data::getDataFromResource
     * @param integer $data
     * @return AbstractModel
     */
    public function getDataFromResource($data)
    {
        return $data;
    }

    /**
     * @see Object\ClassDefinition\Data::getDataForQueryResource
     * @param AbstractModel $data
     * @param null|Model\Object\AbstractObject $object
     * @return integer|null
     */
    public function getDataForQueryResource($data, $object = null)
    {
        return $data;
    }

    /**
     * @see Object\ClassDefinition\Data::getDataForEditmode
     * @param AbstractModel $data
     * @param null|Model\Object\AbstractObject $object
     * @return integer
     */
    public function getDataForEditmode($data, $object = null, $objectFromVersion = null)
    {
        return $this->getDataForResource($data, $object);
    }

    /**
     * @see Model\Object\ClassDefinition\Data::getDataFromEditmode
     * @param integer $data
     * @param null|Model\Object\AbstractObject $object
     * @return AbstractModel
     */
    public function getDataFromEditmode($data, $object = null)
    {
        return $this->getDataFromResource($data);
    }

    /**
     * @param Model\Object\Concrete $data
     * @return bool
     */
    public function isEmpty($data)
    {
        return !$data;
    }

    /**
     * @param $object
     * @return int|string
     */
    public function getDataForSearchIndex($object)
    {
        return parent::getDataForSearchIndex($object);
    }
}
