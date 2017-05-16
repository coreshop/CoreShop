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

namespace CoreShop\Model\Product;

use CoreShop\Model\PriceRule\Condition;
use CoreShop\Model\PriceRule\Action;
use CoreShop\Composite\Dispatcher;

/**
 * Class PriceRule
 * @package CoreShop\Model\Product
 */
class PriceRule extends AbstractProductPriceRule
{
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
    public static $type = "productPriceRule";

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
     * @param Dispatcher $dispatcher
     */
    protected static function initConditionDispatcher(Dispatcher $dispatcher)
    {
        $dispatcher->addTypes([
            Condition\Conditions::class,
            Condition\Customers::class,
            Condition\TimeSpan::class,
            Condition\Quantity::class,
            Condition\Countries::class,
            Condition\Products::class,
            Condition\Categories::class,
            Condition\Customers::class,
            Condition\Zones::class,
            Condition\Personas::class,
            Condition\Shops::class,
            Condition\Currencies::class,
            Condition\CustomerGroups::class
        ]);
    }

    /**
     * @param Dispatcher $dispatcher
     */
    protected static function initActionDispatcher(Dispatcher $dispatcher)
    {
        $dispatcher->addTypes([
            Action\DiscountAmount::class,
            Action\DiscountPercent::class
        ]);
    }

    /**
     * @deprecated will be removed with 1.3
     *
     * @param $condition
     */
    public static function addCondition($condition)
    {
        $class = '\\CoreShop\\Model\\PriceRule\\Condition\\' . ucfirst($condition);

        static::getConditionDispatcher()->addType($class);
    }

    /**
     * @deprecated will be removed with 1.3
     *
     * @param $action
     */
    public static function addAction($action)
    {
        $class = '\\CoreShop\\Model\\PriceRule\\Action\\' . ucfirst($action);

        static::getActionDispatcher()->addType($class);
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
