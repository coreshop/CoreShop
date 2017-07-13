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

namespace CoreShop\Bundle\ShippingBundle\Validator;

use CoreShop\Bundle\ShippingBundle\Checker\CarrierShippingRuleCheckerInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleGroupInterface;

class ShippingRuleCarrierValidator implements ShippableCarrierValidatorInterface
{
    /**
     * @var CarrierShippingRuleCheckerInterface
     */
    private $carrierShippingRuleChecker;

    /**
     * @param CarrierShippingRuleCheckerInterface $carrierShippingRuleChecker
     */
    public function __construct(
        CarrierShippingRuleCheckerInterface $carrierShippingRuleChecker
    )
    {
        $this->carrierShippingRuleChecker = $carrierShippingRuleChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function isCarrierValid(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address) {
        return null != $this->carrierShippingRuleChecker->isShippingRuleValid($carrier, $shippable, $address);
    }
}
