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

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Model\TranslatableTrait;
use CoreShop\Component\Rule\Model\RuleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @psalm-suppress MissingConstructor
 */
class CartPriceRule implements CartPriceRuleInterface
{
    use RuleTrait {
        initializeRuleCollections as private initializeRules;
    }
    use TranslatableTrait {
        initializeTranslationCollection as private initializeTranslationsCollection;

        getTranslation as private doGetTranslation;
    }

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var bool
     */
    protected $isVoucherRule = true;

    /**
     * @var int
     */
    protected $usagePerVoucherCode = 1;

    protected int $priority = 0;

    /**
     * @var Collection|CartPriceRuleVoucherCodeInterface[]
     */
    protected $voucherCodes;

    public function __construct(
        ) {
        $this->initializeRules();
        $this->initializeTranslationsCollection();

        $this->voucherCodes = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getIsVoucherRule()
    {
        return $this->isVoucherRule;
    }

    public function setIsVoucherRule($isVoucherRule)
    {
        $this->isVoucherRule = $isVoucherRule;

        return $this;
    }

    public function getVoucherCodes()
    {
        return $this->voucherCodes;
    }

    public function hasVoucherCodes()
    {
        return !$this->voucherCodes->isEmpty();
    }

    public function addVoucherCode(CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode)
    {
        if (!$this->hasVoucherCode($cartPriceRuleVoucherCode)) {
            $this->voucherCodes->add($cartPriceRuleVoucherCode);
            $cartPriceRuleVoucherCode->setCartPriceRule($this);
        }
    }

    public function removeVoucherCode(CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode)
    {
        if ($this->hasVoucherCode($cartPriceRuleVoucherCode)) {
            $this->voucherCodes->removeElement($cartPriceRuleVoucherCode);
            $cartPriceRuleVoucherCode->setCartPriceRule(null);
        }
    }

    public function hasVoucherCode(CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode)
    {
        return $this->voucherCodes->contains($cartPriceRuleVoucherCode);
    }

    public function getLabel(?string $language = null)
    {
        return $this->getTranslation($language)->getLabel();
    }

    public function setLabel(string $label, ?string $language = null)
    {
        $this->getTranslation($language)->setLabel($label);
    }

    public function getTranslation(?string $locale = null, bool $useFallbackTranslation = true): CartPriceRuleTranslationInterface
    {
        /** @var CartPriceRuleTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale, $useFallbackTranslation);

        return $translation;
    }

    protected function createTranslation(): CartPriceRuleTranslationInterface
    {
        return new CartPriceRuleTranslation();
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }
}
