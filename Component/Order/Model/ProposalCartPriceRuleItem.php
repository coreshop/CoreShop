<?php

namespace CoreShop\Component\Order\Pimcore\Model;

use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreFieldcollection;

class ProposalCartPriceRuleItem extends AbstractPimcoreFieldcollection implements ProposalCartPriceRuleItemInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getObject()->getId() . '_cart_price_rule_' . $this->getIndex();
    }

    /**
     * {@inheritdoc}
     */
    public function getCartPriceRule()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setCartPriceRule($cartPriceRule)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getVoucherCode()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setVoucherCode($voucherCode)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount($withTax = true)
    {
        return $withTax ? $this->getDiscountGross() : $this->getDiscountNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscount($discount, $withTax = true)
    {
        return $withTax ? $this->setDiscountGross($discount) : $this->setDiscountNet($discount);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscountNet($discountNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscountGross($discountGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}