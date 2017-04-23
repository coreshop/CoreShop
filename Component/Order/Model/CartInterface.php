<?php

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Order\Checkout\CheckoutAwareInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface CartInterface extends ProposalInterface, PimcoreModelInterface, CheckoutAwareInterface
{
    /**
     * @param $order
     */
    public function setOrder($order);

    /**
     * @return mixed
     */
    public function getOrder();

    /**
     * @return mixed
     */
    public function getCarrier();

    /**
     * @param $carrier
     *
     * @return mixed
     */
    public function setCarrier($carrier);

    /**
     * @return mixed
     */
    public function getPaymentProvider();

    /**
     * @param $paymentProvider
     *
     * @return mixed
     */
    public function setPaymentProvider($paymentProvider);

    /**
     * @return array
     */
    public function getPriceRuleItems();

    /**
     * @param array $priceRuleItems
     */
    public function setPriceRuleItems($priceRuleItems);

    /**
     * @return array
     */
    public function getPriceRules();

    /**
     * @return bool
     */
    public function hasPriceRules();

    /**
     * @param $priceRule
     */
    public function addPriceRule($priceRule);

    /**
     * @param $priceRule
     */
    public function removePriceRule($priceRule);

    /**
     * @param $priceRule
     *
     * @return bool
     */
    public function hasPriceRule($priceRule);
}
