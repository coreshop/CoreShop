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

use CoreShop\Bundle\CoreBundle\Templating\Helper\ProductQuantityPriceRuleRangesPriceHelperInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\QuantityRangeInterface;
use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
use CoreShop\Component\Core\Provider\DefaultTaxAddressProviderInterface;
use CoreShop\Component\Core\Taxation\TaxApplicatorInterface;
use CoreShop\Component\Order\Calculator\PurchasableCalculatorInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\ProductQuantityPriceRules\Detector\QuantityReferenceDetectorInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ProductQuantityPriceRuleRangesPriceExtension extends AbstractExtension
{
    protected $quantityReferenceDetector;
    protected $purchasableCalculator;
    private $defaultTaxAddressProvider;
    private $taxCalculatorFactory;
    private $taxApplicator;

    public function __construct(
        QuantityReferenceDetectorInterface $quantityReferenceDetector,
        PurchasableCalculatorInterface $purchasableCalculator,
        DefaultTaxAddressProviderInterface $defaultTaxAddressProvider,
        ProductTaxCalculatorFactoryInterface $taxCalculatorFactory,
        TaxApplicatorInterface $taxApplicator
    ) {
        $this->quantityReferenceDetector = $quantityReferenceDetector;
        $this->purchasableCalculator = $purchasableCalculator;
        $this->defaultTaxAddressProvider = $defaultTaxAddressProvider;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
        $this->taxApplicator = $taxApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('coreshop_quantity_price_rule_range_price', [$this, 'getQuantityPriceRuleRangePrice']),
        ];
    }

    public function getQuantityPriceRuleRangePrice(
        QuantityRangeInterface $range,
        ProductInterface $product,
        array $context,
        bool $withTax = true
    ): int {
        $realItemPrice = $this->purchasableCalculator->getPrice($product, $context);
        $price = $this->quantityReferenceDetector->detectRangePrice($product, $range, $realItemPrice, $context);

        $taxCalculator = $this->getTaxCalculator($product, $context);

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            return $this->taxApplicator->applyTax($price, $context, $taxCalculator, $withTax);
        }

        return $price;
    }

    protected function getTaxCalculator(PurchasableInterface $product, array $context): ?TaxCalculatorInterface
    {
        return $this->taxCalculatorFactory->getTaxCalculator($product, $this->getDefaultAddress($context));
    }

    protected function getDefaultAddress(array $context): ?AddressInterface
    {
        return $this->defaultTaxAddressProvider->getAddress($context);
    }
}
