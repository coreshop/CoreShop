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

namespace CoreShop\Component\Core\Shipping\Discover;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Repository\CarrierRepositoryInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Resolver\CarriersResolverInterface;
use CoreShop\Component\Shipping\Validator\ShippableCarrierValidatorInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Webmozart\Assert\Assert;

final class StoreBasedShippableCarriersDiscovery implements CarriersResolverInterface
{
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * @var ShippableCarrierValidatorInterface
     */
    private $shippableCarrierValidator;

    /**
     * @param CarrierRepositoryInterface $carrierRepository
     * @param ShippableCarrierValidatorInterface $shippableCarrierValidator
     */
    public function __construct(
        CarrierRepositoryInterface $carrierRepository,
        ShippableCarrierValidatorInterface $shippableCarrierValidator
    )
    {
        $this->carrierRepository = $carrierRepository;
        $this->shippableCarrierValidator = $shippableCarrierValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveCarriers(ShippableInterface $shippable, AddressInterface $address)
    {
        /**
         * @var CartInterface $shippable
         */
        Assert::isInstanceOf($shippable, CartInterface::class);

        $carriers = $this->carrierRepository->findForStore($shippable->getStore());
        $availableCarriers = [];

        foreach ($carriers as $carrier) {
            if ($this->shippableCarrierValidator->isCarrierValid($carrier, $shippable, $address)) {
                $availableCarriers[] = $carrier;
            }
        }

        return $availableCarriers;
    }
}
