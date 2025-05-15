<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Shipping\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

final class PostcodeConditionChecker extends AbstractConditionChecker
{
    public function isShippingRuleValid(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, array $configuration): bool
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
     */
    private function checkPostCode($postcode, $deliveryPostcode): bool
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
                    $fromNumber = (int) preg_replace('/\D/', '', $fromPart);
                    $toNumber = (int) preg_replace('/\D/', '', $toPart);

                    if ($fromNumber < $toNumber) {
                        $postcodes = [];

                        for ($i = $fromNumber; $i <= $toNumber; ++$i) {
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
