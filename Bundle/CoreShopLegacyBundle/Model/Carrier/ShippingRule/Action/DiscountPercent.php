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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier\ShippingRule\Action;

use CoreShop\Bundle\CoreShopLegacyBundle\Model\Cart;
use CoreShop\Bundle\CoreShopLegacyBundle\Model;

/**
 * Class DiscountPercent
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier\ShippingRule\Action
 */
class DiscountPercent extends AbstractAction
{
    /**
     * @var string
     */
    public static $type = 'discountPercent';

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
     * @param Model\Carrier $carrier
     * @param Cart $cart
     * @param Model\User\Address $address
     * @param float $price
     *
     * @return float
     */
    public function getPriceModification(Model\Carrier $carrier, Cart $cart, Model\User\Address $address, $price)
    {
        return \CoreShop\Bundle\CoreShopLegacyBundle\CoreShop::getTools()->convertToCurrency(-1 * ($price * ($this->getPercent() / 100)), \CoreShop\Bundle\CoreShopLegacyBundle\CoreShop::getTools()->getCurrency(), Model\Currency::getById($this->getCurrency()));
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
