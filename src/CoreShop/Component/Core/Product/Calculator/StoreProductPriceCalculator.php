<?php
declare(strict_types=1);

namespace CoreShop\Component\Core\Product\Calculator;

use CoreShop\Component\Product\Calculator\ProductRetailPriceCalculatorInterface;
use CoreShop\Component\Product\Exception\NoRetailPriceFoundException;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Webmozart\Assert\Assert;

final class StoreProductPriceCalculator implements ProductRetailPriceCalculatorInterface
{
    public function getRetailPrice(ProductInterface $product, array $context): int
    {
        /**
         * @var \CoreShop\Component\Core\Model\ProductInterface $product
         */
        Assert::isInstanceOf($product, \CoreShop\Component\Core\Model\ProductInterface::class);
        Assert::keyExists($context, 'store');
        Assert::isInstanceOf($context['store'], StoreInterface::class);

        $storeValues = $product->getStoreValuesForStore($context['store']);

        if (null === $storeValues) {
            throw new NoRetailPriceFoundException(__CLASS__);
        }

        if (0 === $storeValues->getPrice()) {
            throw new NoRetailPriceFoundException(__CLASS__);
        }

        return $storeValues->getPrice();
    }
}
