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
namespace CoreShop\Model\Product\PriceRule\Condition;

use CoreShop\Model\Product\PriceRule;
use CoreShop\Model\Product;

class Category extends AbstractCondition
{
    /**
     * @var int
     */
    public $category;

    /**
     * @var string
     */
    public $type = 'category';

    /**
     * @return \CoreShop\Model\Category
     */
    public function getCategory()
    {
        if (!$this->category instanceof \CoreShop\Model\Category) {
            $this->category = \CoreShop\Model\Category::getByPath($this->category);
        }

        return $this->category;
    }

    /**
     * @param int $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * Check if Product is Valid for Condition.
     *
     * @param Product    $product
     * @param Product\AbstractProductPriceRule $priceRule
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function checkCondition(Product $product, Product\AbstractProductPriceRule $priceRule)
    {
        return $product->inCategory($this->getCategory());
    }
}
