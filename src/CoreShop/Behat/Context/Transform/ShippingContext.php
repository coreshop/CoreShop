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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Repository\CarrierRepositoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use Webmozart\Assert\Assert;

final class ShippingContext implements Context
{
    private $sharedStorage;
    private $carrierRepository;
    private $shippingRuleRepository;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        CarrierRepositoryInterface $carrierRepository,
        RepositoryInterface $shippingRuleRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->carrierRepository = $carrierRepository;
        $this->shippingRuleRepository = $shippingRuleRepository;
    }

    /**
     * @Transform /^carrier "([^"]+)"$/
     */
    public function getCarrierByName($name)
    {
        $carriers = $this->carrierRepository->findBy(['identifier' => $name]);

        Assert::eq(
            count($carriers),
            1,
            sprintf('%d carriers has been found with name "%s".', count($carriers), $name)
        );

        return reset($carriers);
    }

    /**
     * @Transform /^carrier$/
     */
    public function getLatestCarrier()
    {
        return $this->sharedStorage->get('carrier');
    }

    /**
     * @Transform /^shipping rule "([^"]+)"$/
     */
    public function getShippingRuleByName($ruleName)
    {
        $rule = $this->shippingRuleRepository->findOneBy(['name' => $ruleName]);

        Assert::isInstanceOf($rule, ShippingRuleInterface::class);

        return $rule;
    }

    /**
     * @Transform /^(shipping rule)$/
     */
    public function getLatestShippingRule()
    {
        $resource = $this->sharedStorage->get('shipping-rule');

        Assert::isInstanceOf($resource, ShippingRuleInterface::class);

        return $resource;
    }
}
