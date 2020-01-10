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

namespace CoreShop\Component\Shipping\Rule\Action;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use CoreShop\Component\Shipping\Rule\Processor\ShippingRuleActionProcessorInterface;

class ShippingRuleActionProcessor implements CarrierPriceActionProcessorInterface
{
    /**
     * @var ShippingRuleActionProcessorInterface
     */
    protected $shippingRuleProcessor;

    /**
     * @var RepositoryInterface
     */
    protected $shippingRuleRepository;

    /**
     * @param ShippingRuleActionProcessorInterface $shippingRuleProcessor
     * @param RepositoryInterface                  $shippingRuleRepository
     */
    public function __construct(ShippingRuleActionProcessorInterface $shippingRuleProcessor, RepositoryInterface $shippingRuleRepository)
    {
        $this->shippingRuleProcessor = $shippingRuleProcessor;
        $this->shippingRuleRepository = $shippingRuleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, array $configuration)
    {
        $shippingRule = $this->shippingRuleRepository->find($configuration['shippingRule']);

        if ($shippingRule instanceof ShippingRuleInterface) {
            return $this->shippingRuleProcessor->getPrice($shippingRule, $carrier, $shippable, $address);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getModification(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, $price, array $configuration)
    {
        $shippingRule = $this->shippingRuleRepository->find($configuration['shippingRule']);

        if ($shippingRule instanceof ShippingRuleInterface) {
            return $this->shippingRuleProcessor->getModification($shippingRule, $carrier, $shippable, $address, $price);
        }

        return 0;
    }
}
