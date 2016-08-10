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
 * Class Postcodes
 * @package CoreShop\Model\Carrier\ShippingRule\Condition
 */
class Postcodes extends AbstractCondition
{
    /**
     * @var string
     */
    public $type = 'postcodes';

    /**
     * @var string
     */
    public $postcodes;


    /**
     * Check if Cart is Valid for Condition.
     *
     * @param Model\Cart $cart
     * @param Model\User\Address $address;
     * @param ShippingRule $shippingRule
     *
     * @return mixed
     */
    public function checkCondition(Model\Cart $cart, Model\User\Address $address, ShippingRule $shippingRule)
    {
        $postcodes = $this->getPostcodes();
        $postcodes = explode(",", $postcodes);

        $deliveryAddress = Tool::getDeliveryAddress();

        if ($deliveryAddress->getZip()) {
            foreach ($postcodes as $postcode) {
                //Substring postcode to have the same length
                $deliveryZip = substr($deliveryAddress->getZip(), 0, strlen($postcode));

                if (strtolower($deliveryZip) === strtolower($postcode)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getPostcodes()
    {
        return $this->postcodes;
    }

    /**
     * @param string $postcodes
     */
    public function setPostcodes($postcodes)
    {
        $this->postcodes = $postcodes;
    }
}
