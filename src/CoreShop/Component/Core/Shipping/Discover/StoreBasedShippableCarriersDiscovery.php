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

namespace CoreShop\Component\Core\Shipping\Discover;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Repository\CarrierRepositoryInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Resolver\CarriersResolverInterface;
use CoreShop\Component\Shipping\Validator\ShippableCarrierValidatorInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;

final class StoreBasedShippableCarriersDiscovery implements CarriersResolverInterface
{
    public function __construct(
        private CarriersResolverInterface $inner,
        private CarrierRepositoryInterface $carrierRepository,
        private ShippableCarrierValidatorInterface $shippableCarrierValidator,
    ) {
    }

    public function resolveCarriers(ShippableInterface $shippable, AddressInterface $address): array
    {
        if ($shippable instanceof StoreAwareInterface) {
            $carriers = $this->carrierRepository->findForStore($shippable->getStore());
            $availableCarriers = [];

            foreach ($carriers as $carrier) {
                if ($this->shippableCarrierValidator->isCarrierValid($carrier, $shippable, $address)) {
                    $availableCarriers[] = $carrier;
                }
            }

            return $availableCarriers;
        }

        return $this->inner->resolveCarriers($shippable, $address);
    }
}
