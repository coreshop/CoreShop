<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use Pimcore\Model\DataObject\Fieldcollection;

interface CartInterface extends ProposalInterface, PimcoreModelInterface, StorageListInterface
{
    /**
     * @param OrderInterface $order
     */
    public function setOrder($order);

    /**
     * @return OrderInterface|null
     */
    public function getOrder();

    /**
     * @return Fieldcollection
     */
    public function getPriceRuleItems();

    /**
     * @param Fieldcollection $priceRuleItems
     */
    public function setPriceRuleItems($priceRuleItems);

    /**
     * @return ProposalCartPriceRuleItemInterface[]
     */
    public function getPriceRules();

    /**
     * @return bool
     */
    public function hasPriceRules();

    /**
     * @param ProposalCartPriceRuleItemInterface $priceRule
     */
    public function addPriceRule(ProposalCartPriceRuleItemInterface $priceRule);

    /**
     * @param ProposalCartPriceRuleItemInterface $priceRule
     */
    public function removePriceRule(ProposalCartPriceRuleItemInterface $priceRule);

    /**
     * @param ProposalCartPriceRuleItemInterface $priceRule
     *
     * @return bool
     */
    public function hasPriceRule(ProposalCartPriceRuleItemInterface $priceRule);

    /**
     * @param CartPriceRuleInterface                 $cartPriceRule
     * @param CartPriceRuleVoucherCodeInterface|null $voucherCode
     *
     * @return bool
     */
    public function hasCartPriceRule(CartPriceRuleInterface $cartPriceRule, CartPriceRuleVoucherCodeInterface $voucherCode = null);

    /**
     * @param CartPriceRuleInterface                 $cartPriceRule
     * @param CartPriceRuleVoucherCodeInterface|null $voucherCode
     *
     * @return ProposalCartPriceRuleItemInterface|null
     */
    public function getPriceRuleByCartPriceRule(CartPriceRuleInterface $cartPriceRule, CartPriceRuleVoucherCodeInterface $voucherCode = null);

    /**
     * @return PaymentProviderInterface
     */
    public function getPaymentProvider();

    /**
     * @param PaymentProviderInterface $paymentProvider
     *
     * @return PaymentProviderInterface
     */
    public function setPaymentProvider($paymentProvider);

    /**
     * @return int
     */
    public function getPaymentTotal();

    /**
     * @param int $paymentTotal
     */
    public function setPaymentTotal($paymentTotal);
}
