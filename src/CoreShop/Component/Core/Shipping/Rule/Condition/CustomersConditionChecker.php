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

namespace CoreShop\Component\Core\Shipping\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Rule\Condition\AbstractConditionChecker;
use Webmozart\Assert\Assert;

final class CustomersConditionChecker extends AbstractConditionChecker
{
    /**
     * {@inheritdoc}
     */
    public function isShippingRuleValid(
        CarrierInterface $carrier,
        ShippableInterface $shippable,
        AddressInterface $address,
        array $configuration
    ) {
        /**
         * @var $shippable CartInterface
         */
        Assert::isInstanceOf($shippable, CartInterface::class);

        if (!$shippable->getCustomer() instanceof CustomerInterface) {
            return false;
        }

        return in_array($shippable->getCustomer()->getId(), $configuration['customers']);
    }
}
