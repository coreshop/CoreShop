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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Product;

use CoreShop\Model\Product;

class SpecificPrice extends AbstractProductPriceRule
{
    /**
     * possible types of a condition.
     *
     * @var array
     */
    public static $availableConditions = array('customer', 'timeSpan', 'country', 'customerGroup', 'zone', 'quantity');

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
     * Get all PriceRules.
     *
     * @param Product $product
     *
     * @return array
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

        return $list->getData();
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
}
