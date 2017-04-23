<?php

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Rule\Model\RuleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CartPriceRule implements CartPriceRuleInterface
{
    use RuleTrait;

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
    protected $active = false;

    /**
     * @var bool
     */
    protected $highlight = false;

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
    public function getActive()
    {
        return $this->active;
    }

    /**
     * {@inheritdoc}
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHighlight()
    {
        return $this->highlight;
    }

    /**
     * {@inheritdoc}
     */
    public function setHighlight($highlight)
    {
        $this->highlight = $highlight;

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
}
