<?php

namespace CoreShop\Bundle\ProductBundle\Calculator;

use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

final class ProductPriceCalculator implements ProductPriceCalculatorInterface
{
    /**
     * @var PrioritizedServiceRegistryInterface
     */
    private $priceCalculatorRegistry;

    /**
     * ProductPriceCalculator constructor.
     * @param PrioritizedServiceRegistryInterface $priceCalculatorRegistry
     */
    public function __construct(PrioritizedServiceRegistryInterface $priceCalculatorRegistry)
    {
        $this->priceCalculatorRegistry = $priceCalculatorRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice($subject)
    {
        $price = false;

        foreach ($this->priceCalculatorRegistry->all() as $calculator) {
            $calcPrice = $calculator->getPrice($subject);

            if (false !== $calcPrice && null !== $calcPrice) {
                $price = $calcPrice;
            }
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount($subject, $price)
    {
        $discount = 0;

        foreach ($this->priceCalculatorRegistry->all() as $calculator) {
            $discount += $calculator->getDiscount($subject, $price);
        }

        return $discount;
    }
}
