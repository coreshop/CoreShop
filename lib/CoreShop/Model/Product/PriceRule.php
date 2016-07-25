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

namespace CoreShop\Model\Product;

use CoreShop\Model\Product;
use CoreShop\Tool;

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
    public static $availableConditions = array('customer', 'timeSpan', 'quantity', 'country', 'product', 'category', 'customerGroup', 'zone', 'persona');

    /**
     * possible types of a action.
     *
     * @var array
     */
    public static $availableActions = array('discountAmount', 'discountPercent');


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
        $list->setCondition("active = ?", array($active));

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
