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

namespace CoreShop\Maintenance;

use CoreShop\Exception;
use CoreShop\Model\Cart;

/**
 * Class CleanUpCart
 * @package CoreShop\Maintenance
 */
class CleanUpCart
{
    /**
     * @var array
     */
    private static $params = [];

    /***
     * @var array
     */
    private static $errors = [];

    /**
     * @param array $params
     */
    public function setOptions($params = [])
    {
        $defaults = [
            'olderThanDays' => 30,
        ];

        self::$params = array_merge($defaults, $params);

        if (!isset(self::$params['deleteAnonymousCart']) && !isset(self::$params['deleteUserCart'])) {
            self::$errors[] = 'Either Anonymous, User or both types needs to be set.';
        }
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return count(self::$errors) > 0;
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
     *
     * @throws Exception
     */
    public function getCartElements()
    {
        if ($this->hasErrors()) {
            throw new Exception('Some options are missing, please check errors.');
        }

        $list = Cart::getList();

        $conditions = [];
        $groupCondition = [];
        $params = [];

        $daysTimestamp = new \Pimcore\Date();
        $daysTimestamp->subDay(self::$params['olderThanDays']);

        $conditions[] = 'o_creationDate < ?';
        $params[] = $daysTimestamp->getTimestamp();

        //Never delete carts with a order
        $conditions[] = 'order__id IS NULL';

        if (self::$params['deleteAnonymousCart']) {
            $groupCondition[] = 'user__id IS NULL';
        }
        if (self::$params['deleteUserCart']) {
            $groupCondition[] = 'user__id IS NOT NULL';
        }

        $bind = ' AND ';
        $groupBind = ' AND ';

        $sql = implode($bind, $conditions);

        if (count($groupCondition) > 1) {
            $groupBind = ' OR ';
        }

        $sql .= ' AND ('.implode($groupBind, $groupCondition).') ';

        $list->setCondition($sql, $params);
        $carts = $list->load();

        return $carts;
    }

    /**
     * @param Cart $cart
     *
     * @return bool
     */
    public function deleteCart(Cart $cart)
    {
        $cart->delete();

        return true;
    }
}
