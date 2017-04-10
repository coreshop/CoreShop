<?php

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface CartInterface extends ProposalInterface, PimcoreModelInterface
{
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
