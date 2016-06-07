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
use CoreShop\Model\Cart\PriceRule;

class SpecificPrice extends AbstractProductPriceRule
{
    /**
     * possible types of a condition.
     *
     * @var array
     */
    public static $availableConditions = array('customer', 'timeSpan', 'country', 'customerGroup', 'zone');

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
     * Get all PriceRules.
     *
     * @param Product $product
     *
     * @return array
     */
    public static function getSpecificPrices(Product $product)
    {
        $list = new SpecificPrice\Listing();
        $list->setCondition('o_id = ?', array($product->getId()));

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
}
