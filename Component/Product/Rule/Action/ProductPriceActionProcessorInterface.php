<?php

namespace CoreShop\Component\Product\Rule\Action;

interface ProductPriceActionProcessorInterface
{
    /**
     * @param $subject
     * @param $price
     * @param array $configuration
     * @return mixed
     */
    public function getDiscount($subject, $price, array $configuration);

    /**
     * @param $subject
     * @param array $configuration
     *
     * @return mixed
     */
    public function getPrice($subject, array $configuration);
}
