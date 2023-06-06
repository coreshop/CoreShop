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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Payment\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use CoreShop\Component\Resource\Model\ToggleableTrait;
use CoreShop\Component\Resource\Model\TranslatableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Pimcore\Model\Asset;

/**
 * @psalm-suppress MissingConstructor
 */
class PaymentProvider extends AbstractResource implements PaymentProviderInterface, \Stringable
{
    use TimestampableTrait;
    use ToggleableTrait;
    use TranslatableTrait {
        __construct as initializeTranslationsCollection;

        getTranslation as private doGetTranslation;
    }

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var int
     */
    protected $position = 1;

    /**
     * @var Asset|null
     */
    protected $logo;

    /**
     * @var Collection|PaymentProviderRuleGroupInterface[]
     */
    protected $paymentProviderRules;

    public function __construct(
        ) {
        $this->initializeTranslationsCollection();
        $this->paymentProviderRules = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('%s', $this->getIdentifier());
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

    public function getTitle($language = null)
    {
        return $this->getTranslation($language)->getTitle();
    }

    public function setTitle($title, $language = null)
    {
        $this->getTranslation($language)->setTitle($title);
    }

    public function getDescription($language = null)
    {
        return $this->getTranslation($language)->getDescription();
    }

    public function setDescription($description, $language = null)
    {
        $this->getTranslation($language)->setDescription($description);
    }

    public function getInstructions($language = null)
    {
        return $this->getTranslation($language)->getInstructions();
    }

    public function setInstructions($instructions, $language = null)
    {
        $this->getTranslation($language)->setInstructions($instructions);
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    public function getTranslation(?string $locale = null, bool $useFallbackTranslation = true): PaymentProviderTranslationInterface
    {
        /** @var PaymentProviderTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale, $useFallbackTranslation);

        return $translation;
    }

    protected function createTranslation(): PaymentProviderTranslationInterface
    {
        return new PaymentProviderTranslation();
    }

    public function getPaymentProviderRules()
    {
        return $this->paymentProviderRules;
    }

    public function hasPaymentProviderRules()
    {
        return !$this->paymentProviderRules->isEmpty();
    }

    public function hasPaymentProviderRule(PaymentProviderRuleGroupInterface $paymentProviderRuleGroup)
    {
        return $this->paymentProviderRules->contains($paymentProviderRuleGroup);
    }

    public function addPaymentProviderRule(PaymentProviderRuleGroupInterface $paymentProviderRuleGroup)
    {
        if (!$this->hasPaymentProviderRule($paymentProviderRuleGroup)) {
            $this->paymentProviderRules->add($paymentProviderRuleGroup);
        }
    }

    public function removePaymentProviderRule(PaymentProviderRuleGroupInterface $paymentProviderRuleGroup)
    {
        if ($this->hasPaymentProviderRule($paymentProviderRuleGroup)) {
            $this->paymentProviderRules->removeElement($paymentProviderRuleGroup);
            $paymentProviderRuleGroup->setPaymentProvider(null);
        }
    }
}
