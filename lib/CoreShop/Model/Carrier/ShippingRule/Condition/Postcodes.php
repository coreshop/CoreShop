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
     * @var boolean
     */
    public $exclusion;


    /**
     * Check if Cart is Valid for Condition.
     *
     * @param Model\Carrier $carrier
     * @param Model\Cart $cart
     * @param Model\User\Address $address;
     * @param ShippingRule $shippingRule
     *
     * @return mixed
     */
    public function checkCondition(Model\Carrier $carrier, Model\Cart $cart, Model\User\Address $address, ShippingRule $shippingRule)
    {
        $postcodes = $this->getPostcodes();
        $postcodes = explode(",", $postcodes);

        $deliveryAddress = $address;

        if ($deliveryAddress->getZip()) {
            foreach ($postcodes as $postcode) {
                if ($this->checkPostCode($postcode, $deliveryAddress->getZip())) {
                    return $this->getExclusion() ? false : true;
                }
            }
        }

        return $this->getExclusion() ? true : false;
    }

    /**
     * @param $postcode
     * @param $deliveryPostcode
     * @return bool
     */
    protected function checkPostCode($postcode, $deliveryPostcode)
    {
        //Check if postcode has a range
        $deliveryPostcode = str_replace(' ', '', $deliveryPostcode);
        $postcodes = [$postcode];

        if (strpos($postcode, '-') > 0) {
            $splitted = explode('-', $postcode); //We should now have 2 elements

            if (count($splitted) === 2) {
                $from = $splitted[0];
                $to = $splitted[1];

                $fromText = preg_replace('/[0-9]+/', '', $from);
                $toText = preg_replace('/[0-9]+/', '', $to);

                if ($fromText === $toText) {
                    $fromNumber = preg_replace('/\D/', '', $from);
                    $toNumber = preg_replace('/\D/', '', $to);

                    if ($fromNumber < $toNumber) {
                        $postcodes = [];

                        for ($i = $fromNumber; $i <= $toNumber; $i++) {
                            $postcodes[] = $fromText . $i;
                        }
                    }
                }
            }
        }

        foreach ($postcodes as $postcode) {
            $deliveryZip = substr($deliveryPostcode, 0, strlen($postcode));

            if (strtolower($deliveryZip) === strtolower($postcode)) {
                return true;
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

    /**
     * @return boolean
     */
    public function getExclusion()
    {
        return $this->exclusion;
    }

    /**
     * @param boolean $exclusion
     */
    public function setExclusion($exclusion)
    {
        $this->exclusion = $exclusion;
    }
}
