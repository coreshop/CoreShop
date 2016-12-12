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

namespace CoreShop\Model\Cart;

use CoreShop\Exception;
use CoreShop\Model\AbstractModel;
use CoreShop\Model\Cart;
use CoreShop\Model\Currency;
use CoreShop\Model\Order;
use CoreShop\Model\PriceRule\AbstractPriceRule;
use CoreShop\Model\PriceRule\Action\AbstractAction;
use CoreShop\Model\PriceRule\Condition\AbstractCondition;
use CoreShop\Model\PriceRule\Item as PriceRuleItem;
use CoreShop\Model\Shop;
use CoreShop\Model\User;
use Pimcore\Model\Object\Service;

/**
 * Class Manager
 * @package CoreShop\Model\Cart
 */
class Manager
{
    /**
     * get all carts for a specific user
     *
     * @param User $user
     *
     * @return Cart[]
     */
    public function getCarts(User $user) {
        $list = Cart::getList();
        $list->setCondition("user__id = ? AND order__id is null", [$user->getId()]);
        $list->load();

        return $list->getObjects();
    }

    /**
     * get a cart by its name
     *
     * @param $name
     * @param User $user
     * @return null
     */
    public function getByName($name, User $user) {
        $list = Cart::getList();
        $list->setCondition("user__id = ? AND name = ? AND order__id is null", [$user->getId(), $name]);
        $list->load();

        if($list->getTotalCount() > 0) {
            $objects = $list->getObjects();

            return $objects[0];
        }

        return null;
    }

    /**
     * delete a specific cart
     *
     * @param Cart $cart
     */
    public function deleteCart(Cart $cart) {
        $cart->delete();
    }

    /**
     * @param Cart $cart
     */
    public function setSessionCart(Cart $cart) {
        $cartSession = \CoreShop::getTools()->getSession();

        $cartSession->cartObj = $cart;
        $cartSession->cartId = null;
    }

    /**
     * get session cart
     *
     * @return Cart|null
     */
    public function getSessionCart() {
        $cartSession = \CoreShop::getTools()->getSession();
        $cart = null;

        if (isset($cartSession->cartId) && $cartSession->cartId !== 0) {
            return  Cart::getById($cartSession->cartId);
        } elseif (isset($cartSession->cartObj)) {
            if ($cartSession->cartObj instanceof Cart) {
                return $cartSession->cartObj;
            }
        }

        return null;
    }

    /**
     * create a new cart for a user
     *
     * @param string $name
     * @param User|null $user
     * @param Shop|null $shop
     * @param Currency|null $currency
     * @param boolean $persist

     * @return Cart
     */
    public function createCart($name = "default", User $user = null, Shop $shop = null, Currency $currency = null, $persist = false) {
        $cart = Cart::create();
        $cart->setKey(uniqid());
        $cart->setPublished(true);
        $cart->setShop($shop ? $shop : Shop::getShop());
        $cart->setName($name);
        $cart->setCurrency(is_null($currency) ? \CoreShop::getTools()->getCurrency() : $currency);

        if ($cart instanceof Cart) {
            if ($user instanceof User) {
                $cart->setUser($user);
            }
        }

        if($persist) {
            $this->persistCart($cart);
        }

        return $cart;
    }

    /**
     * Persists a cart in the db
     *
     * @param Cart $cart
     */
    public function persistCart(Cart $cart) {
        $cartsFolder = Service::createFolderByPath('/coreshop/carts/' . date('Y/m/d'));
        $cart->setParent($cartsFolder);
        $cart->save();
    }
}
