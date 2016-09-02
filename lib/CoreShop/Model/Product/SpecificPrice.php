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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Product;

use CoreShop\Model\Product;
use Pimcore\Model\Object\AbstractObject;

/**
 * Class SpecificPrice
 * @package CoreShop\Model\Product
 */
class SpecificPrice extends AbstractProductPriceRule
{
    /**
     * possible types of a condition.
     *
     * @var array
     */
    public static $availableConditions = array('customers', 'timeSpan', 'countries', 'customerGroups', 'zones', 'quantity', 'personas', 'shops', 'currencies');

    /**
     * possible types of a action.
     *
     * @var array
     */
    public static $availableActions = array('discountAmount', 'discountPercent', 'newPrice');

    /**
     * @var string
     */
    public static $type = "specificprice";

    /**
     * @var int
     */
    public $o_id;

    /**
     * @var boolean
     */
    public $inherit;

    /**
     * @var int
     */
    public $priority;

    /**
     * Get all PriceRules.
     *
     * @param Product $product
     *
     * @return self[]
     */
    public static function getSpecificPrices(Product $product)
    {
        $list = SpecificPrice::getList();

        $query = "";
        $queryParams = [
            $product->getId()
        ];

        if ($product->getType() === Product::OBJECT_TYPE_VARIANT) {
            $parentIds = $product->getParentIds();

            $query = "OR (o_id in (" . implode(",", $parentIds) . ") AND inherit = 1)";
        }

        $list->setCondition("o_id = ? " . $query, $queryParams);
        $list->setOrder("DESC");
        $list->setOrderKey("priority");

        return $list->getData();
    }

    public function save()
    {
        parent::save();

        $object = AbstractObject::getById($this->getO_Id());

        if ($object instanceof Product) {
            $object->clearPriceCache();
        }
    }

    /**
     * @return int
     */
    public function getO_Id()
    {
        return $this->o_id;
    }

    /**
     * @param int $o_id
     */
    public function setO_Id($o_id)
    {
        $this->o_id = $o_id;
    }

    /**
     * @return boolean
     */
    public function getInherit()
    {
        return $this->inherit;
    }

    /**
     * @param boolean $inherit
     */
    public function setInherit($inherit)
    {
        $this->inherit = $inherit;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }
}
