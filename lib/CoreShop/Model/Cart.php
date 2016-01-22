<?php
/**
 * CoreShop
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

namespace CoreShop\Model;

use CoreShop\Exception\UnsupportedException;
use CoreShop\Plugin;
use CoreShop\Tool;
use CoreShop\Model\PriceRule;

use Pimcore\Model\Object\CoreShopCart;
use Pimcore\Model\Object\CoreShopCartItem;
use Pimcore\Model\Object\Service;

class Cart extends Base {

    /**
     * Return Cart by custom identifier
     *
     * @param $transactionIdentification
     * @return bool|Cart
     */
    public static function findByCustomIdentifier($transactionIdentification) {
        $list = CoreShopCart::getByCustomIdentifier($transactionIdentification);

        $carts = $list->getObjects();

        if (count($carts) > 0) {
            return $carts[0];
        }

        return false;
    }

    /**
     * Get all existing Carts
     *
     * @return array CoreShopCart
     */
    public static function getAll()
    {
        $list = new CoreShopCart\Listing();
        
        return $list->getObjects();
    }

    /**
     * Prepare a Cart
     *
     * @return CoreShopCart
     * @throws \Exception
     */
    public static function prepare()
    {
        $cartsFolder = Service::createFolderByPath("/coreshop/carts/" . date("Y/m/d"));
        
        $cart = CoreShopCart::create();
        $cart->setKey(uniqid());
        $cart->setParent($cartsFolder);
        $cart->setPublished(true);

        if(Tool::getUser() instanceof User) {
            $cart->setUser(Tool::getUser());
        }

        $cart->save();

        return $cart;
    }

    /**
     * Check if Cart has any physical items
     *
     * @return bool
     */
    public function hasPhysicalItems()
    {
        foreach($this->getItems() as $item)
        {
            if($item->getProduct()->getIsDownloadProduct() !== "yes")
            {
                return true;
            }
        }
        
        return false;
    }

    /**
     * calculates discount for the cart
     *
     * @return int
     */
    public function getDiscount()
    {
        $priceRule = $this->getPriceRule();

        if($priceRule instanceof PriceRule)
            return $priceRule->getDiscount();

        return 0;
    }

    /**
     * calculates the subtotal for the cart
     *
     * @return int
     */
    public function getSubtotal()
    {
        $subtotal = 0;
        
        foreach($this->getItems() as $item)
        {
            $subtotal += ($item->getAmount() * $item->getProduct()->getPrice());
        }
        
        return $subtotal;
    }

    /**
     * calculates shipping costs for the cart
     *
     * @return int
     */
    public function getShipping()
    {
        if(count($this->getItems()) === 0) {
            return 0;
        }

        $session = Tool::getSession();

        //check for existing shipping
        if($this->getCarrier() instanceof Carrier) {
            return $this->getCarrier()->getDeliveryPrice($this);
        }

        //get all provider and choose cheapest
        $providers = Carrier::getCarriersForCart($this);
        $cheapestProvider = null;

        foreach($providers as $p)
        {
            if($cheapestProvider === null)
                $cheapestProvider = $p;
            else if($cheapestProvider->getDeliveryPrice($this) > $p->getDeliveryPrice($this))
                $cheapestProvider = $p;
        }

        if($cheapestProvider instanceof Carrier)
            return $cheapestProvider->getDeliveryPrice($this);

        return 0;
    }

    /**
     * calculates the total of the cart
     *
     * @return int
     */
    public function getTotal()
    {
        $subtotal = $this->getSubtotal();
        $discount = $this->getDiscount();
        $shipping = $this->getShipping();

        return ($subtotal + $shipping) - $discount;
    }

    /**
     * calculates the total weight of the cart
     *
     * @return int
     */
    public function getTotalWeight()
    {
        $weight = 0;

        foreach($this->getItems() as $item)
        {
            $weight += ($item->getAmount() * $item->getProduct()->getWeight());
        }

        return $weight;
    }

    /**
     * finds the CartItem for a Product
     *
     * @param Product $product
     * @return bool
     * @throws \Exception
     */
    public function findItemForProduct(Product $product)
    {
        if (!$product instanceof Product)
            throw new \Exception("\$product must be instance of Product");

        foreach ($this->getItems() as $item){
            if($item->getProduct()->getId() == $product->getId())
                return $item;
        }

        return false;
    }

    /**
     * Changes the quantity of a Product in the Cart
     *
     * @param Product $product
     * @param int $amount
     * @param bool|false $increaseAmount
     * @param bool|true $autoAddPriceRule
     * @return bool|CoreShopCartItem
     * @throws \Exception
     */
    public function updateQuantity(Product $product, $amount = 0, $increaseAmount = false, $autoAddPriceRule = true)
    {
        if(!$product instanceof Product)
            throw new \Exception("\$product must be instance of Product");

        $item = $this->findItemForProduct($product);

        if($item instanceof CartItem)
        {
            if($amount <= 0) {
                $this->removeItem($item);

                return false;

            } else {

                $newAmount = $amount;

                if( $increaseAmount === TRUE ) {

                    $currentAmount = $item->getAmount();

                    if( is_integer( $currentAmount ) ) {
                        $newAmount = $currentAmount + $amount;
                    }

                }

                $item->setAmount( $newAmount );
                $item->save();
            }
        }
        else
        {
            $items = $this->getItems();

            if(!is_array($items))
                $items = array();

            $item = new CoreShopCartItem();
            $item->setKey(uniqid());
            $item->setParent($this);
            $item->setAmount($amount);
            $item->setProduct($product);
            $item->setPublished(true);
            $item->save();

            $items[] = $item;

            $this->setItems($items);
            $this->save();
        }

        if($autoAddPriceRule)
            PriceRule::autoAddToCart();

        return $item;
    }

    /**
     * Adds a new item to the cart
     *
     * @param Product $product
     * @param int $amount
     * @return bool|CoreShopCartItem
     * @throws \Exception
     */
    public function addItem(Product $product, $amount = 1)
    {
        return $this->updateQuantity($product, $amount, true);
    }

    /**
     * Removes a item from the cart
     *
     * @param CartItem $item
     */
    public function removeItem(CartItem $item)
    {
        $item->delete();
    }

    /**
     * Modifies the quantity of a CartItem
     *
     * @param CartItem $item
     * @param $amount
     * @return bool|CartItem
     * @throws \Exception
     */
    public function modifyItem(CartItem $item, $amount)
    {
        return $this->updateQuantity($item->getProduct(), $amount, false);
    }

    /**
     * Removes an existing PriceRule from the cart
     *
     * @return bool
     * @throws \Exception
     */
    public function removePriceRule()
    {
        if($this->getPriceRule() instanceof PriceRule)
        {
            $this->getPriceRule()->unApplyRules();

            $this->setPriceRule(null);
            $this->save();
        }

        return true;
    }

    /**
     * Adds a new PriceRule to the Cart
     *
     * @param \CoreShop\Model\PriceRule $priceRule
     * @throws \Exception
     */
    public function addPriceRule(PriceRule $priceRule)
    {
        $this->removePriceRule();
        $this->setPriceRule($priceRule);
        $this->getPriceRule()->applyRules();

        $this->save();
    }

    /**
     * Returns the cart as array
     *
     * @return array
     */
    public function toArray()
    {
        $items = array();
        
        foreach($this->getItems() as $item)
        {
            $items[] = $item->toArray();
        }
        
        return array(
            "user" => $this->getUser() ? $this->getUser()->toArray() : null,
            "items" => $items,
            "subtotal" => Tool::formatPrice($this->getSubtotal()),
            "total" => Tool::formatPrice($this->getTotal())
        );
    }


    /**
     * returns array cart items
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return CartItem[]
     */
    public function getItems() {
        throw new UnsupportedException("getItems is not supported for " . get_class($this));
    }

    /**
     * returns active price rule for cart
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return PriceRule
     */
    public function getPriceRule() {
        throw new UnsupportedException("getPriceRule is not supported for " . get_class($this));
    }

    /**
     * sets price rule for this cart
     * this method has to be overwritten in Pimcore Object
     *
     * @param $priceRule
     * @throws UnsupportedException
     * @return PriceRule
     */
    public function setPriceRule($priceRule) {
        throw new UnsupportedException("setPriceRule is not supported for " . get_class($this));
    }

    /**
     * returns user for this cart
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return User
     */
    public function getUser() {
        throw new UnsupportedException("getUser is not supported for " . get_class($this));
    }
}