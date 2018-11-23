<?php

namespace CoreShop\Component\Core\Taxation;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use Webmozart\Assert\Assert;

final class TaxApplicator implements TaxApplicatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function applyTax($price, array $context, TaxCalculatorInterface $taxCalculator, $withTax = true)
    {
        Assert::keyExists($context, 'store');
        Assert::isInstanceOf($context['store'], StoreInterface::class);

        $useGrossPrice = $context['store']->getUseGrossPrice();

        if ($useGrossPrice) {
            if ($withTax) {
                return $price;
            }

            return $taxCalculator->removeTaxes($price);
        }

        if ($withTax) {
            return $taxCalculator->applyTaxes($price);
        }

        return $price;
    }
}
