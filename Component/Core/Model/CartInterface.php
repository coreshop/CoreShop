<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Order\Model\CartInterface as BaseCartInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

interface CartInterface extends BaseCartInterface, ShippableInterface
{
    /**
     * @param CurrencyInterface $currency
     */
    public function setCurrency($currency);

    /**
     * @return CurrencyInterface
     */
    public function getCurrency();

    /**
     * @param \CoreShop\Component\Store\Model\StoreInterface $store
     */
    public function setStore($store);

    /**
     * @return StoreInterface
     */
    public function getStore();
}
