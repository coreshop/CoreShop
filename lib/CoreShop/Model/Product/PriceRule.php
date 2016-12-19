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

/**
 * Class PriceRule
 * @package CoreShop\Model\Product
 */
class PriceRule extends AbstractProductPriceRule
{
    /**
     * possible types of a condition.
     *
     * @var array
    */
    public static $availableConditions = ['conditions', 'customers', 'timeSpan', 'quantity', 'countries', 'products', 'categories', 'customerGroups', 'zones', 'personas', 'shops', 'currencies'];

    /**
     * possible types of a action.
     *
     * @var array
     */
    public static $availableActions = ['discountAmount', 'discountPercent'];


    /**
     * @var string
     */
    public $description;

    /**
     * @var bool
     */
    public $active;

    /**
     * @var string
     */
    public static $type = "pricerule";

    /**
     * Get al PriceRules.
     *
     * @param boolean $active
     * @return array
     */
    public static function getPriceRules($active = true)
    {
        $list = PriceRule::getList();
        $list->setCondition("active = ?", [$active]);

        return $list->getData();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }
}
