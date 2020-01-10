<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Shipping\Processor;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use CoreShop\Component\Shipping\Rule\Action\CarrierPriceActionProcessorInterface;
use CoreShop\Component\Shipping\Rule\Processor\ShippingRuleActionProcessorInterface;

class ShippingRuleActionProcessor implements ShippingRuleActionProcessorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $actionServiceRegistry;

    /**
     * @param ServiceRegistryInterface $actionServiceRegistry
     */
    public function __construct(ServiceRegistryInterface $actionServiceRegistry)
    {
        $this->actionServiceRegistry = $actionServiceRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(ShippingRuleInterface $shippingRule, CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address)
    {
        $price = 0;

        foreach ($shippingRule->getActions() as $action) {
            $processor = $this->actionServiceRegistry->get($action->getType());

            if ($processor instanceof CarrierPriceActionProcessorInterface) {
                $price += $processor->getPrice($carrier, $shippable, $address, $action->getConfiguration());
            }
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getModification(ShippingRuleInterface $shippingRule, CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, $price)
    {
        $modifications = 0;

        foreach ($shippingRule->getActions() as $action) {
            $processor = $this->actionServiceRegistry->get($action->getType());

            if ($processor instanceof CarrierPriceActionProcessorInterface) {
                $modifications += $processor->getModification($carrier, $shippable, $address, $price, $action->getConfiguration());
            }
        }

        return $modifications;
    }
}
