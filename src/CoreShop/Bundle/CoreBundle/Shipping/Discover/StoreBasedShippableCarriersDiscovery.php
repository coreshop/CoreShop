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

namespace CoreShop\Bundle\CoreBundle\Shipping\Discover;

use CoreShop\Bundle\ShippingBundle\Checker\CarrierShippingRuleCheckerInterface;
use CoreShop\Bundle\ShippingBundle\Discover\ShippableCarriersDiscoveryInterface;
use CoreShop\Bundle\ShippingBundle\Validator\ShippableCarrierValidatorInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Repository\CarrierRepositoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleGroupInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;

final class StoreBasedShippableCarriersDiscovery implements ShippableCarriersDiscoveryInterface
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
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @param CarrierRepositoryInterface $carrierRepository
     * @param ShippableCarrierValidatorInterface $shippableCarrierValidator
     * @param StoreContextInterface $storeContext
     */
    public function __construct(
        CarrierRepositoryInterface $carrierRepository,
        ShippableCarrierValidatorInterface $shippableCarrierValidator,
        StoreContextInterface $storeContext
    )
    {
        $this->carrierRepository = $carrierRepository;
        $this->shippableCarrierValidator = $shippableCarrierValidator;
        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function discoverCarriers(ShippableInterface $shippable, AddressInterface $address)
    {
        $carriers = $this->carrierRepository->findForStore($this->storeContext->getStore());
        $availableCarriers = [];

        foreach ($carriers as $carrier) {
            if ($this->shippableCarrierValidator->isCarrierValid($carrier, $shippable, $address)) {
                $availableCarriers[] = $carrier;
            }
        }

        return $availableCarriers;
    }
}
