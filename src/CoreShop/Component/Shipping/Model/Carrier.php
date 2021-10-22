<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
     * @var bool
     */
    private $isFree = false;

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

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->shippingRules = new ArrayCollection();
    }

    public function getId()
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

    public function getDescription($language = null)
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

    public function getIsFree()
    {
        return $this->isFree;
    }

    public function setIsFree($isFree)
    {
        $this->isFree = $isFree;
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
