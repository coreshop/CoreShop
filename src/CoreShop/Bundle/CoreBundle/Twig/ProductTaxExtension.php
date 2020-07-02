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

declare(strict_types=1);

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
    private $productPriceCalculator;
    private $taxCalculatorFactory;
    private $defaultAddressProvider;

    public function __construct(
        TaxedProductPriceCalculatorInterface $productPriceCalculator,
        ProductTaxCalculatorFactoryInterface $taxCalculatorFactory,
        DefaultTaxAddressProviderInterface $defaultAddressProvider
    )
    {
        $this->productPriceCalculator = $productPriceCalculator;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
        $this->defaultAddressProvider = $defaultAddressProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('coreshop_product_tax_amount', [$this, 'getTaxAmount']),
            new TwigFilter('coreshop_product_tax_rate', [$this, 'getTaxRate']),
        ];
    }

    public function getTaxAmount(PurchasableInterface $product, array $context = []): int
    {
        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product, $this->defaultAddressProvider->getAddress($context));

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $taxCalculator->getTaxesAmount($this->productPriceCalculator->getPrice($product, $context, false));
        }

        return 0;
    }

    public function getTaxRate(PurchasableInterface $product, array $context = []): float
    {
        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator($product, $this->defaultAddressProvider->getAddress($context));

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $taxCalculator->getTotalRate();
        }

        return 0;
    }
}
