<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Shipping\Resolver;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Validator\ShippableCarrierValidatorInterface;

final class CarriersResolver implements CarriersResolverInterface
{
    /**
     * @var RepositoryInterface
     */
    private $carrierRepository;

    /**
     * @var ShippableCarrierValidatorInterface
     */
    private $shippableCarrierValidator;

    /**
     * @param RepositoryInterface                $carrierRepository
     * @param ShippableCarrierValidatorInterface $shippableCarrierValidator
     */
    public function __construct(
        RepositoryInterface $carrierRepository,
        ShippableCarrierValidatorInterface $shippableCarrierValidator
    ) {
        $this->carrierRepository = $carrierRepository;
        $this->shippableCarrierValidator = $shippableCarrierValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveCarriers(ShippableInterface $shippable, AddressInterface $address)
    {
        /**
         * @var CarrierInterface[] $carriers
         */
        $carriers = $this->carrierRepository->findAll();
        $availableCarriers = [];

        foreach ($carriers as $carrier) {
            if ($this->shippableCarrierValidator->isCarrierValid($carrier, $shippable, $address)) {
                $availableCarriers[] = $carrier;
            }
        }

        return $availableCarriers;
    }
}
