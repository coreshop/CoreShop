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

namespace CoreShop\Component\Order\Model;

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
