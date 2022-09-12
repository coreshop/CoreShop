<?php
declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use CoreShop\Component\Core\Provider\DefaultTaxAddressProviderInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ProductTaxExtension extends AbstractExtension
{
    public function __construct(
        private TaxedProductPriceCalculatorInterface $productPriceCalculator,
        private ProductTaxCalculatorFactoryInterface $taxCalculatorFactory,
        private DefaultTaxAddressProviderInterface $defaultAddressProvider,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('coreshop_product_tax_amount', [$this, 'getTaxAmount']),
            new TwigFilter('coreshop_product_tax_rate', [$this, 'getTaxRate']),
        ];
    }

    public function getTaxAmount(PurchasableInterface $product, array $context = []): int
    {
        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product, $this->defaultAddressProvider->getAddress($context), $context);

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $taxCalculator->getTaxesAmount($this->productPriceCalculator->getPrice($product, $context, false));
        }

        return 0;
    }

    public function getTaxRate(PurchasableInterface $product, array $context = []): float
    {
        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product, $this->defaultAddressProvider->getAddress($context), $context);

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $taxCalculator->getTotalRate();
        }

        return 0;
    }
}
