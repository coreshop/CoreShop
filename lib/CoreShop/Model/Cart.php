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

namespace CoreShop\Model;

use CoreShop\Plugin;
use CoreShop\Tool;
use CoreShop\Model\PriceRule;
use CoreShop\Model\Plugin\Shipping;

use Pimcore\Model\Object\CoreShopProduct;
use Pimcore\Model\Object\CoreShopCart;
use Pimcore\Model\Object\CoreShopCartItem;
use Pimcore\Model\Object\CoreShopUser;
use Pimcore\Model\Object\Service;

class Cart extends Base {

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

        if(Tool::getUser() instanceof CoreShopUser) {
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
        $cartRule = $this->getPriceRule();

        if($cartRule instanceof PriceRule)
            return $cartRule->getDiscount();

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
            $subtotal += ($item->getAmount() * $item->getProduct()->getProductPrice());
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
        $session = Tool::getSession();

        //check for existing shipping
        if(array_key_exists("shippingProvider", $session->order) && $session->order['deliveryProvider'] instanceof Shipping) {
            return $session->order['shippingProvider']->getShipping($this);
        }

        //get all provider and choose cheapest
        $providers = Plugin::getShippingProviders($this);
        $cheapestProvider = null;

        foreach($providers as $p)
        {
            if($cheapestProvider === null)
                $cheapestProvider = $p;
            else if($cheapestProvider->getShipping($this) > $p->getShipping($this))
                $cheapestProvider = $p;
        }

        if($cheapestProvider instanceof Shipping)
            return $cheapestProvider->getShipping($this);

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

        return ($subtotal  + $shipping) - $discount;
    }

    /**
     * finds the CartItem for a Product
     *
     * @param CoreShopProduct $product
     * @return bool
     * @throws \Exception
     */
    public function findItemForProduct(CoreShopProduct $product)
    {
        if (!$product instanceof CoreShopProduct)
            throw new \Exception("\$product must be instance of CoreShopProduct");

        foreach ($this->getItems() as $item){
            if($item->getProduct()->getId() == $product->getId())
                return $item;
        }

        return false;
    }

    /**
     * Changes the quantity of a Product in the Cart
     *
     * @param CoreShopProduct $product
     * @param int $amount
     * @param bool|true $autoAddCartRule
     * @return bool|CoreShopCartItem
     * @throws \Exception
     */
    public function updateQuantity(CoreShopProduct $product, $amount = 0, $autoAddCartRule = true)
    {
        if(!$product instanceof CoreShopProduct)
            throw new \Exception("\$product must be instance of CoreShopProduct");

        $item = $this->findItemForProduct($product);

        if($item instanceof CoreShopCartItem)
        {
            if($amount <= 0) {
                $this->removeItem($item);

                return false;
            }
            else {
                $item->setAmount($amount);
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
            $this->save(true);
        }

        if($autoAddCartRule)
            PriceRule::autoAddToCart();

        return $item;
    }

    /**
     * Adds a new item to the cart
     *
     * @param CoreShopProduct $product
     * @param int $amount
     * @return bool|CoreShopCartItem
     * @throws \Exception
     */
    public function addItem(CoreShopProduct $product, $amount = 1)
    {
        return $this->updateQuantity($product, $amount);
    }

    /**
     * Removes a item from the cart
     *
     * @param CoreShopCartItem $item
     */
    public function removeItem(CoreShopCartItem $item)
    {
        $item->delete();
    }

    /**
     * Modifies the quantity of a CartItem
     *
     * @param CoreShopCartItem $item
     * @param $amount
     * @return bool|CoreShopCartItem
     * @throws \Exception
     */
    public function modifyItem(CoreShopCartItem $item, $amount)
    {
        return $this->updateQuantity($item->getProduct(), $amount);
    }

    /**
     * Removes an existing PriceRule from the cart
     *
     * @deprecated since 1.0, replace by removePriceRule
     *
     * @return bool
     * @throws \Exception
     */
    public function removeCartRule()
    {
        $this->removePriceRule();
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
            /*if($this->getCartRule()->getFreeGift() instanceof CoreShopProduct)
            {
                $this->updateQuantity($this->getCartRule()->getFreeGift(), 0, false);
            }*/

            $this->getPriceRule()->unApplyRules();

            $this->setPriceRule(null);
            $this->save();
        }

        return true;
    }

    /**
     * Adds a new PriceRule to the Cart
     *
     * @deprecated: since 1.0, replace by addPriceRule
     *
     * @param \CoreShop\Model\PriceRule $cartRule
     * @throws \Exception
     */
    public function addCartRule(PriceRule $cartRule)
    {
        $this->addPriceRule($cartRule);
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
}