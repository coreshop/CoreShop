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

declare(strict_types=1);

namespace CoreShop\Component\Shipping\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;

interface ShippingRuleGroupInterface extends ResourceInterface, TimestampableInterface
{
    /**
     * @return CarrierInterface
     */
    public function getCarrier();

    /**
     * @param CarrierInterface|null $carrier
     */
    public function setCarrier(CarrierInterface $carrier = null);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $priority
     */
    public function setPriority($priority);

    /**
     * @return bool
     */
    public function getStopPropagation();

    /**
     * @param bool $stopPropagation
     */
    public function setStopPropagation($stopPropagation);

    /**
     * @return ShippingRuleInterface
     */
    public function getShippingRule();

    /**
     * @param ShippingRuleInterface $shippingRule
     */
    public function setShippingRule(ShippingRuleInterface $shippingRule);
}
