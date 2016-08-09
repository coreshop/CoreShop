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

namespace CoreShop\Model\Carrier\ShippingRule\Condition;

use CoreShop\Model;
use CoreShop\Model\Carrier\ShippingRule;
use CoreShop\Tool;

/**
 * Class Dimension
 * @package CoreShop\Model\Carrier\ShippingRule\Condition
 */
class Dimension extends AbstractCondition
{
    /**
     * @var string
     */
    public $type = 'dimension';

    /**
     * @var float
     */
    public $height;

    /**
     * @var float
     */
    public $width;

    /**
     * @var float
     */
    public $depth;

    /**
     * Check if Cart is Valid for Condition.
     *
     * @param Model\Cart $cart
     * @param Model\User\Address $address;
     * @param ShippingRule $shippingRule
     *
     * @return mixed
     */
    public function checkCondition(Model\Cart $cart, Model\User\Address $address, ShippingRule $shippingRule) {
        foreach($cart->getItems() as $item) {
            $product = $item->getProduct();

            if($product instanceof Model\Product) {
                if ($this->getHeight() > 0) {
                    if($product->getHeight() > $this->getHeight())
                        return false;
                }

                if ($this->getDepth() > 0) {
                    if($product->getDepth() > $this->getDepth())
                        return false;
                }

                if ($this->getWidth() > 0) {
                    if($product->getWidth() > $this->getWidth())
                        return false;
                }
            }
        }

        return true;
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param float $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param float $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return float
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @param float $depth
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
    }
}
