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

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Model\TranslatableTrait;
use CoreShop\Component\Rule\Model\RuleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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

    /**
     * @var Collection|CartPriceRuleVoucherCodeInterface[]
     */
    protected $voucherCodes;

    public function __construct()
    {
        $this->initializeRules();
        $this->initializeTranslationsCollection();

        $this->voucherCodes = new ArrayCollection();
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsVoucherRule()
    {
        return $this->isVoucherRule;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsVoucherRule($isVoucherRule)
    {
        $this->isVoucherRule = $isVoucherRule;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getVoucherCodes()
    {
        return $this->voucherCodes;
    }

    /**
     * {@inheritdoc}
     */
    public function hasVoucherCodes()
    {
        return !$this->voucherCodes->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addVoucherCode(CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode)
    {
        if (!$this->hasVoucherCode($cartPriceRuleVoucherCode)) {
            $this->voucherCodes->add($cartPriceRuleVoucherCode);
            $cartPriceRuleVoucherCode->setCartPriceRule($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeVoucherCode(CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode)
    {
        if ($this->hasVoucherCode($cartPriceRuleVoucherCode)) {
            $this->voucherCodes->removeElement($cartPriceRuleVoucherCode);
            $cartPriceRuleVoucherCode->setCartPriceRule(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasVoucherCode(CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode)
    {
        return $this->voucherCodes->contains($cartPriceRuleVoucherCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel($language = null)
    {
        return $this->getTranslation($language)->getLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label, $language = null)
    {
        $this->getTranslation($language)->setLabel($label);
    }

    /**
     * @param null $locale
     * @param bool $useFallbackTranslation
     *
     * @return CartPriceRuleTranslationInterface
     */
    public function getTranslation($locale = null, $useFallbackTranslation = true)
    {
        /** @var CartPriceRuleTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale, $useFallbackTranslation);

        return $translation;
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation()
    {
        return new CartPriceRuleTranslation();
    }
}
