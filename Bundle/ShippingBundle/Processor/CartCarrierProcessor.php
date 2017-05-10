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

namespace CoreShop\Bundle\ShippingBundle\Processor;

use CoreShop\Bundle\ShippingBundle\Checker\CarrierShippingRuleCheckerInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleGroupInterface;

class CartCarrierProcessor implements CartCarrierProcessorInterface
{
    /**
     * @var RepositoryInterface
     */
    private $carrierRepository;

    /**
     * @var CarrierShippingRuleCheckerInterface
     */
    private $carrierShippingRuleChecker;

    /**
     * @param RepositoryInterface                 $carrierRepository
     * @param CarrierShippingRuleCheckerInterface $carrierShippingRuleChecker
     */
    public function __construct(
        RepositoryInterface $carrierRepository,
        CarrierShippingRuleCheckerInterface $carrierShippingRuleChecker
    ) {
        $this->carrierRepository = $carrierRepository;
        $this->carrierShippingRuleChecker = $carrierShippingRuleChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function getCarriersForCart(ShippableInterface $shippable, AddressInterface $address = null)
    {
        $carriers = $this->carrierRepository->findAll(); //TODO: restrict to store, but needs to be moved to CoreBundle to make it work
        $availableCarriers = [];

        //First: Get all available carriers
        foreach ($carriers as $carrier) {
            if ($this->carrierShippingRuleChecker->isShippingRuleValid($carrier, $cart, $address) instanceof ShippingRuleGroupInterface) {
                $availableCarriers[] = $carrier;
            }
        }

        return $availableCarriers;
    }
}
