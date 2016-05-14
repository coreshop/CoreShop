<?php

namespace CoreShop\Maintenance;

use CoreShop\Model\Cart;

class CleanUpCart {

    /**
     * @var array
     */
    private static $params = array();

    /***
     * @var array
     */
    private static $errors = array();

    /**
     * @param array $params
     */
    public function setOptions( $params = array() ) {

        $defaults = array(
            "olderThanDays" => 30
        );

        self::$params = array_merge( $defaults, $params);

        if( !isset( self::$params["deleteAnonymousCart"]) && !isset( self::$params["deleteUserCart"] ) ) {
            self::$errors[] = "Either Anonymous, User or both types needs to be set.";
        }

    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return count( self::$errors) > 0;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return self::$errors;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getCartElements()
    {
        if( $this->hasErrors()) {
            throw new \Exception("Some options are missing, please check errors.");
        }

        $list = Cart::getList();

        $conditions = array();
        $groupCondition = array();
        $params = array();

        $daysTimestamp = new \Pimcore\Date();
        $daysTimestamp->subDay(self::$params["olderThanDays"]);

        $conditions[] = "o_creationDate < ?";
        $params[] = $daysTimestamp->getTimestamp();

        if(self::$params["deleteAnonymousCart"]) {
            $groupCondition[] = "user__id IS NULL";
        }
        if(self::$params["deleteUserCart"]) {
            $groupCondition[] = "user__id IS NOT NULL";
        }

        $bind = " AND ";
        $groupBind = " AND ";

        $sql = implode($bind, $conditions);

        if( count( $groupCondition ) > 1) {
            $groupBind = " OR ";
        }

        $sql .= " AND (" . implode( $groupBind, $groupCondition ) . ") ";

        $list->setCondition( $sql, $params);
        $carts = $list->load();

        return $carts;

    }

    /**
     * @param Cart $cart
     *
     * @return bool
     */
    public function deleteCart(Cart $cart) {

        $cart->delete();

        return true;

    }
}