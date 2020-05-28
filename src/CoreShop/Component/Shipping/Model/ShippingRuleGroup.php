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

declare(strict_types=1);

namespace CoreShop\Component\Shipping\Model;

use CoreShop\Component\Resource\Model\SetValuesTrait;
use CoreShop\Component\Resource\Model\TimestampableTrait;

class ShippingRuleGroup implements ShippingRuleGroupInterface
{
    use TimestampableTrait;
    use SetValuesTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var CarrierInterface
     */
    private $carrier;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var bool
     */
    private $stopPropagation = false;

    /**
     * @var ShippingRuleInterface
     */
    private $shippingRule;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * {@inheritdoc}
     */
    public function setCarrier(CarrierInterface $carrier = null)
    {
        $this->carrier = $carrier;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getStopPropagation()
    {
        return $this->stopPropagation;
    }

    /**
     * {@inheritdoc}
     */
    public function setStopPropagation($stopPropagation)
    {
        $this->stopPropagation = $stopPropagation;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingRule()
    {
        return $this->shippingRule;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingRule(ShippingRuleInterface $shippingRule)
    {
        $this->shippingRule = $shippingRule;
    }
}
