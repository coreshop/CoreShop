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

namespace CoreShop\Model\Carrier\ShippingRule\Action;

use CoreShop\Model\Cart;
use CoreShop\Model;
use CoreShop\Tool;

/**
 * Class DiscountPercent
 * @package CoreShop\Model\Carrier\ShippingRule\Action
 */
class DiscountPercent extends AbstractAction
{
    /**
     * @var string
     */
    public $type = 'discountPercent';

    /**
     * @var
     */
    public $percent;

    /**
     * @var int
     */
    public $currency;

    /**
     * get addition/discount for shipping
     *
     * @param Cart $cart
     * @param Model\User\Address $address
     * @param float $price
     *
     * @return float
     */
    public function getPriceModification(Cart $cart, Model\User\Address $address, $price)
    {
        return Tool::convertToCurrency(-1 * ($price * ($this->getPercent() / 100)), Model\Currency::getById($this->getCurrency()));
    }

    /**
     * @return mixed
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * @param mixed $percent
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;
    }

    /**
     * @return int
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param int $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }
}
