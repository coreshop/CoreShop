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

namespace CoreShop\Component\Shipping\Model;

use CoreShop\Component\Resource\Model\SetValuesTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Carrier implements CarrierInterface
{
    use SetValuesTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $trackingUrl;

    /**
     * @var bool
     */
    private $isFree = false;

    /**
     * @var int
     */
    private $rangeBehaviour = self::RANGE_BEHAVIOUR_DEACTIVATE;

    /**
     * @var Collection|ShippingRuleGroupInterface[]
     */
    protected $shippingRules;

    public function __construct()
    {
        $this->shippingRules = new ArrayCollection();
    }

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * {@inheritdoc}
     */
    public function getTrackingUrl()
    {
        return $this->trackingUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setTrackingUrl($trackingUrl)
    {
        $this->trackingUrl = $trackingUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsFree()
    {
        return $this->isFree;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsFree($isFree)
    {
        $this->isFree = $isFree;
    }

    /**
     * {@inheritdoc}
     */
    public function getRangeBehaviour()
    {
        return $this->rangeBehaviour;
    }

    /**
     * {@inheritdoc}
     */
    public function setRangeBehaviour($rangeBehaviour)
    {
        $this->rangeBehaviour = $rangeBehaviour;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingRules()
    {
        return $this->shippingRules;
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippingRules()
    {
        return !$this->shippingRules->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addShippingRule(ShippingRuleGroupInterface $shippingRuleGroup)
    {
        if (!$this->hasShippingRule($shippingRuleGroup)) {
            $this->shippingRules->add($shippingRuleGroup);

            $shippingRuleGroup->setCarrier($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeShippingRule(ShippingRuleGroupInterface $shippingRuleGroup)
    {
        if ($this->hasShippingRule($shippingRuleGroup)) {
            $this->shippingRules->removeElement($shippingRuleGroup);
            $shippingRuleGroup->setCarrier(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippingRule(ShippingRuleGroupInterface $shippingRuleGroup)
    {
        return $this->shippingRules->contains($shippingRuleGroup);
    }
}
