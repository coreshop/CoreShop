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

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use CoreShop\Component\Resource\Model\TranslatableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Carrier extends AbstractResource implements CarrierInterface
{
    use TimestampableTrait;
    use TranslatableTrait {
        __construct as initializeTranslationsCollection;
    }

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $trackingUrl;

    /**
     * @var bool
     */
    private $isFree = false;

    /**
     * @var Collection|ShippingRuleGroupInterface[]
     */
    protected $shippingRules;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

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
        @trigger_error('getName is deprecated since 2.0.0-beta.2 and will be removed in 2.0.0, use getIdentifier instead', E_USER_DEPRECATED);

        return $this->getIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        @trigger_error('setName is deprecated since 2.0.0-beta.2 and will be removed in 2.0.0, use setIdentifier instead', E_USER_DEPRECATED);

        $this->setIdentifier($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription($language = null)
    {
        return $this->getTranslation($language)->getDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description, $language = null)
    {
        $this->getTranslation($language)->setDescription($description);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel($language = null)
    {
        return $this->getTitle($language);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label, $language = null)
    {
        @trigger_error('getLabel is deprecated since 2.0.0-beta.2 and will be removed in 2.0.0, use getTitle instead', E_USER_DEPRECATED);

        $this->setTitle($label, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle($language = null)
    {
        @trigger_error('setLabel is deprecated since 2.0.0-beta.2 and will be removed in 2.0.0, use setTitle instead', E_USER_DEPRECATED);

        return $this->getTranslation($language)->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title, $language = null)
    {
        $this->getTranslation($language)->setTitle($title);
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

    /**
     * {@inheritdoc}
     */
    protected function createTranslation()
    {
        return new CarrierTranslation();
    }
}
