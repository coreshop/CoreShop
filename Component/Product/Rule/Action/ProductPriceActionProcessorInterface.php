<?php

namespace CoreShop\Component\Product\Rule\Action;

interface ProductPriceActionProcessorInterface
{
    /**
     * @param $subject
     * @param array $configuration
     * @param bool  $withTax
     *
     * @return mixed
     */
    public function getDiscount($subject, array $configuration, $withTax = true);

    /**
     * @param $subject
     * @param array $configuration
     *
     * @return mixed
     */
    public function getPrice($subject, array $configuration);
}
