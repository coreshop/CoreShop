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

use CoreShop\Exception;
use CoreShop\Model\Product;
use Pimcore\Cache;
use Pimcore\Logger;
use Pimcore\Model\Object\AbstractObject;
use CoreShop\Model\PriceRule\Condition;
use CoreShop\Model\PriceRule\Action;
use CoreShop\Composite\Dispatcher;

/**
 * Class SpecificPrice
 * @package CoreShop\Model\Product
 */
class SpecificPrice extends AbstractProductPriceRule
{
    /**
     * @var string
     */
    public static $type = "specificPrice";

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
     * @param Dispatcher $dispatcher
     */
    protected static function initConditionDispatcher(Dispatcher $dispatcher)
    {
        $dispatcher->addTypes([
            Condition\Conditions::class,
            Condition\Customers::class,
            Condition\TimeSpan::class,
            Condition\Countries::class,
            Condition\CustomerGroups::class,
            Condition\Zones::class,
            Condition\Personas::class,
            Condition\Shops::class,
            Condition\Currencies::class
        ]);
    }

    /**
     * @param Dispatcher $dispatcher
     */
    protected static function initActionDispatcher(Dispatcher $dispatcher) {
        $dispatcher->addTypes([
            Action\DiscountAmount::class,
            Action\DiscountPercent::class,
            Action\NewPrice::class
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
     * Get all PriceRules.
     *
     * @param Product $product
     *
     * @return self[]
     */
    public static function getSpecificPrices(Product $product)
    {
        $className = static::class;
        $cacheKey = self::getClassCacheKey($className, "prices_for_product_" . $product->getId());

        try {
            $object = \Zend_Registry::get($cacheKey);
            if (!$object) {
                throw new Exception($className.' in registry is null');
            }

            return $object;
        } catch (\Exception $e) {
            try {
                if (!$objects = Cache::load($cacheKey)) {
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

                    $objects = $list->getData();
                    \Zend_Registry::set($cacheKey, $objects);
                    Cache::save($objects, $cacheKey, [$cacheKey, $product->getCacheKey()]);
                } else {
                    \Zend_Registry::set($cacheKey, $objects);
                }

                return $objects;
            } catch (\Exception $e) {
                Logger::warning($e->getMessage());
            }
        }

        return [];
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
