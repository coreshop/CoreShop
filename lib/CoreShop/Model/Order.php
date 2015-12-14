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

use CoreShop\Exception\UnsupportedException;
use Pimcore\Model\Object;
use Pimcore\Model\Object\CoreShopPayment;

class Order extends Base
{
    /**
     * Import a Cart to the Order
     *
     * @param Cart $cart
     * @return bool
     * @throws \Exception
     */
    public function importCart(Cart $cart)
    {
        $items = array();
        $i = 1;
        
        foreach($cart->getItems() as $cartItem)
        {
            $item = new Object\CoreShopOrderItem();
            $item->setKey($i);
            $item->setParent(Object\Service::createFolderByPath($this->getFullPath() . "/items/"));
            $item->setPublished(true);
            
            $item->setProduct($cartItem->getProduct());
            $item->setWholesalePrice($cartItem->getProduct()->getWholesalePrice());
            $item->setRetailPrice($cartItem->getProduct()->getRetailPrice());
            $item->setTax($cartItem->getProduct()->getTax());
            $item->setPrice($cartItem->getProduct()->getProductPrice());
            $item->setAmount($cartItem->getAmount());
            $item->setExtraInformation($cartItem->getExtraInformation());
            $item->save();
            
            $items[] = $item;
            
            $i++;
        }

        $this->setDiscount($cart->getDiscount());
        $this->setPriceRule($cart->getPriceRule());
        $this->setItems($items);
        $this->save();
        
        return true;
    }

    /**
     * Create a new Payment
     *
     * @param Payment $provider
     * @param $amount
     * @return Object\CoreShopPayment
     * @throws \Exception
     */
    public function createPayment(Payment $provider, $amount)
    {
        $payment = new Object\CoreShopPayment();
        $payment->setKey(uniqid());
        $payment->setPublished(true);
        $payment->setParent(Object\Service::createFolderByPath($this->getFullPath() . "/payments/"));
        $payment->setAmount($amount);
        $payment->setTransactionIdentifier(uniqid());
        $payment->setProvider($provider->getIdentifier());
        $payment->save();
        
        $this->addPayment($payment);
        
        return $payment;
    }

    /**
     * Add a new Payment
     *
     * @param CoreShopPayment $payment
     */
    public function addPayment(CoreShopPayment $payment)
    {
        $payments = $this->getPayments();
        
        if(!is_array($payments))
            $payments = array();
            
        $payments[] = $payment;
        
        $this->setPayments($payments);
        $this->save();
    }

    /**
     * Calculates the subtotal of the Order
     *
     * @return int
     */
    public function getSubtotal()
    {
        $total = 0;

        foreach($this->getItems() as $item)
        {
            $total += $item->getTotal();
        }

        return $total;
    }

    /**
     * Calculates the total of the Order
     *
     * @return int
     */
    public function getTotal()
    {
        $subtotal = $this->getSubtotal();
        $shipping = $this->getShipping();
        $discount = $this->getDiscount();

        return ($subtotal  + $shipping) - $discount;
    }

    /**
     * Pimcore: When save is called from Pimcore, check for changes of the OrderState
     *
     * @return int
     */
    public function save() {

        if (isset($_REQUEST['data']) && false) {
            try {
                $data = \Zend_Json::decode($_REQUEST['data']);

                if (isset($data['orderState']))
                {
                    $orderStep = OrderState::getById($data['orderState']);

                    if ($orderStep instanceof OrderState)
                    {
                        $orderStep->processStep($this);
                    }
                }
            } catch (\Exception $ex) {
                \Logger::error($ex);
            }
        }

        parent::save();
    }

    /**
     * set discount for order
     * this method has to be overwritten in Pimcore Object
     *
     * @param OrderState $state
     * @throws UnsupportedException
     */
    public function setOrderState($state) {
        throw new UnsupportedException("setOrderState is not supported for " . get_class($this));
    }

    /**
     * set discount for order
     * this method has to be overwritten in Pimcore Object
     *
     * @param float $discount
     * @throws UnsupportedException
     */
    public function setDiscount($discount) {
        throw new UnsupportedException("setDiscount is not supported for " . get_class($this));
    }

    /**
     * returns discount for order
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return float
     */
    public function getDiscount() {
        throw new UnsupportedException("getDiscount is not supported for " . get_class($this));
    }

    /**
     * returns customer for order
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return User
     */
    public function getCustomer() {
        throw new UnsupportedException("getCustomer is not supported for " . get_class($this));
    }

    /**
     * returns shipping for order
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return float
     */
    public function getShipping() {
        throw new UnsupportedException("getShipping is not supported for " . get_class($this));
    }

    /**
     * set PriceRule for order
     * this method has to be overwritten in Pimcore Object
     *
     * @param PriceRule $priceRule
     * @throws UnsupportedException
     */
    public function setPriceRule($priceRule) {
        throw new UnsupportedException("setPriceRule is not supported for " . get_class($this));
    }

    /**
     * set items for order
     * this method has to be overwritten in Pimcore Object
     *
     * @param OrderItem[] $items
     * @throws UnsupportedException
     */
    public function setItems($items) {
        throw new UnsupportedException("setItems is not supported for " . get_class($this));
    }

    /**
     * returns payments
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return CoreShopPayment[]
     */
    public function getPayments() {
        throw new UnsupportedException("getPayments is not supported for " . get_class($this));
    }

    /**
     * sets payments
     * this method has to be overwritten in Pimcore Object
     *
     * @param CoreShopPayment[] $payments
     * @throws UnsupportedException
     */
    public function setPayments($payments) {
        throw new UnsupportedException("setPayments is not supported for " . get_class($this));
    }

    /**
     * returns orderitems
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return OrderItem[]
     */
    public function getItems() {
        throw new UnsupportedException("getItems is not supported for " . get_class($this));
    }
}