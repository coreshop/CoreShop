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

namespace CoreShop\Component\Shipping\Resolver;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Shipping\Calculator\CarrierPriceCalculatorInterface;
use CoreShop\Component\Shipping\Exception\UnresolvedDefaultCarrierException;
use CoreShop\Component\Shipping\Model\ShippableInterface;

final class CheapestDefaultCarrierResolver implements DefaultCarrierResolverInterface
{
    /**
     * @var CarriersResolverInterface
     */
    private $carriersResolver;

    /**
     * @var CarrierPriceCalculatorInterface
     */
    private $carrierPriceCalculator;

    /**
     * @param CarriersResolverInterface       $carriersResolver
     * @param CarrierPriceCalculatorInterface $carrierPriceCalculator
     */
    public function __construct(
        CarriersResolverInterface $carriersResolver,
        CarrierPriceCalculatorInterface $carrierPriceCalculator
    ) {
        $this->carriersResolver = $carriersResolver;
        $this->carrierPriceCalculator = $carrierPriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCarrier(ShippableInterface $shippable, AddressInterface $address)
    {
        $carriers = $this->carriersResolver->resolveCarriers($shippable, $address);

        if (empty($carriers)) {
            throw new UnresolvedDefaultCarrierException();
        }

        uasort($carriers, function ($a, $b) use ($shippable, $address) {
            $aPrice = $this->carrierPriceCalculator->getPrice($a, $shippable, $address);
            $bPrice = $this->carrierPriceCalculator->getPrice($b, $shippable, $address);

            return $aPrice > $bPrice;
        });

        return reset($carriers);
    }
}
