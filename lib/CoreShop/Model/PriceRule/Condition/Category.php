<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShop\Model\PriceRule\Condition;

use CoreShop\Model\PriceRule;
use CoreShop\Model\Cart;
use Pimcore\Model\Object\CoreShopCategory;

class Category extends AbstractCondition {

    /**
     * @var int
     */
    public $category;

    /**
     * @var string
     */
    public $type = "category";

    /**
     * @return int
     */
    public function getCategory()
    {
        if(!$this->category instanceof CoreShopCategory)
            $this->category = CoreShopCategory::getByPath($this->category);

        return $this->category;
    }

    /**
     * @param int $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function checkCondition(Cart $cart, PriceRule $priceRule, $throwException = false)
    {
        $found = false;

        if($this->getCategory() instanceof CoreShopCategory) {
            foreach ($cart->getItems() as $i) {
                if ($i->getProduct()->inCategory($this->getCategory()))
                    $found = true;
            }
        }

        if(!$found)
            if($throwException) throw new \Exception("You cannot use this voucher with these products"); else return false;

        return true;
    }
}
