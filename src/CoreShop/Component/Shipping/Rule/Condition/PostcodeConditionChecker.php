<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Shipping\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

final class PostcodeConditionChecker extends AbstractConditionChecker
{
    /**
     * {@inheritdoc}
     */
    public function isShippingRuleValid(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, array $configuration)
    {
        $postcodes = explode(',', $configuration['postcodes']);

        $deliveryAddress = $address;

        if ($deliveryAddress->getPostcode()) {
            foreach ($postcodes as $postcode) {
                if ($this->checkPostCode($postcode, $deliveryAddress->getPostcode())) {
                    return $configuration['exclusion'] ? false : true;
                }
            }
        }

        return $configuration['exclusion'] ? true : false;
    }

    /**
     * @param string $postcode
     * @param string $deliveryPostcode
     *
     * @return bool
     */
    private function checkPostCode($postcode, $deliveryPostcode)
    {
        //Check if postcode has a range
        $deliveryPostcode = str_replace(' ', '', $deliveryPostcode);
        $postcodes = [$postcode];

        if (strpos($postcode, '-') > 0) {
            $splitted = explode('-', $postcode); //We should now have 2 elements

            if (count($splitted) === 2) {
                $fromPart = $splitted[0];
                $toPart = $splitted[1];

                $fromText = preg_replace('/[0-9]+/', '', $fromPart);
                $toText = preg_replace('/[0-9]+/', '', $toPart);

                if ($fromText === $toText) {
                    $fromNumber = preg_replace('/\D/', '', $fromPart);
                    $toNumber = preg_replace('/\D/', '', $toPart);

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
}
