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

namespace CoreShop\Component\Shipping\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use CoreShop\Component\Resource\Model\TranslatableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Pimcore\Model\Asset;

/**
 * @psalm-suppress MissingConstructor
 */
class Carrier extends AbstractResource implements CarrierInterface
{
    use TimestampableTrait;
    use TranslatableTrait {
        __construct as initializeTranslationsCollection;

        getTranslation as private doGetTranslation;
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
    private $hideFromCheckout = false;

    /**
     * @var Asset|null
     */
    private $logo;

    /**
     * @var string
     */
    private $taxCalculationStrategy;

    /**
     * @var Collection|ShippingRuleGroupInterface[]
     */
    protected $shippingRules;

    public function __construct(
        ) {
        $this->initializeTranslationsCollection();

        $this->shippingRules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public function getDescription(?string $language = null)
    {
        return $this->getTranslation($language)->getDescription();
    }

    public function setDescription(string $description, ?string $language = null)
    {
        $this->getTranslation($language)->setDescription($description);
    }

    public function getTitle(?string $language = null)
    {
        return $this->getTranslation($language)->getTitle();
    }

    public function setTitle(string $title, ?string $language = null)
    {
        $this->getTranslation($language)->setTitle($title);
    }

    public function getTrackingUrl()
    {
        return $this->trackingUrl;
    }

    public function setTrackingUrl($trackingUrl)
    {
        $this->trackingUrl = $trackingUrl;
    }

    public function getHideFromCheckout()
    {
        return $this->hideFromCheckout;
    }

    public function setHideFromCheckout($hideFromCheckout)
    {
        $this->hideFromCheckout = $hideFromCheckout;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    public function getTaxCalculationStrategy()
    {
        return $this->taxCalculationStrategy;
    }

    public function setTaxCalculationStrategy($taxCalculationStrategy)
    {
        $this->taxCalculationStrategy = $taxCalculationStrategy;
    }

    public function getShippingRules()
    {
        return $this->shippingRules;
    }

    public function hasShippingRules()
    {
        return !$this->shippingRules->isEmpty();
    }

    public function addShippingRule(ShippingRuleGroupInterface $shippingRuleGroup)
    {
        if (!$this->hasShippingRule($shippingRuleGroup)) {
            $this->shippingRules->add($shippingRuleGroup);

            $shippingRuleGroup->setCarrier($this);
        }
    }

    public function removeShippingRule(ShippingRuleGroupInterface $shippingRuleGroup)
    {
        if ($this->hasShippingRule($shippingRuleGroup)) {
            $this->shippingRules->removeElement($shippingRuleGroup);
            $shippingRuleGroup->setCarrier(null);
        }
    }

    public function hasShippingRule(ShippingRuleGroupInterface $shippingRuleGroup)
    {
        return $this->shippingRules->contains($shippingRuleGroup);
    }

    public function getTranslation(?string $locale = null, bool $useFallbackTranslation = true): CarrierTranslationInterface
    {
        /** @var CarrierTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale, $useFallbackTranslation);

        return $translation;
    }

    protected function createTranslation(): CarrierTranslationInterface
    {
        return new CarrierTranslation();
    }
}
