<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Pimcore\Model\DataObject\Folder;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductPriceCalculatorInterface
     */
    private $productPriceCalculator;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ProductRepositoryInterface $productRepository
     * @param ProductPriceCalculatorInterface $productPriceCalculator
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ProductRepositoryInterface $productRepository,
        ProductPriceCalculatorInterface $productPriceCalculator
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->productRepository = $productRepository;
        $this->productPriceCalculator = $productPriceCalculator;
    }

    /**
     * @Then /^the (product "[^"]+") should be priced at ([^"]+)$/
     */
    public function productShouldBePriced(ProductInterface $product, int $price)
    {
        Assert::same(intval($price), $this->productPriceCalculator->getPrice($product, true));
    }

    /**
     * @Then /^the (product "[^"]+") discount-price should be ([^"]+)$/
     */
    public function productsDiscountPriceShouldBe(ProductInterface $product, int $price)
    {
        Assert::same(intval($price), $this->productPriceCalculator->getDiscountPrice($product));
    }

    /**
     * @Then /^the (product "[^"]+") retail-price should be ([^"]+)$/
     */
    public function productsRetailPriceShouldBe(ProductInterface $product, int $price)
    {
        Assert::same(intval($price), $this->productPriceCalculator->getRetailPrice($product));
    }

    /**
     * @Then /^the (product "[^"]+") discount should be ([^"]+)$/
     */
    public function productDiscountShouldBe(ProductInterface $product, int $discount)
    {
        $productPrice = $this->productPriceCalculator->getPrice($product, false);
        $productDiscount = $this->productPriceCalculator->getDiscount($product, $productPrice);

        Assert::same($discount, $productDiscount);
    }

    /**
     * @Then /^the (product "[^"]+") should have the prices, price: ([^"]+) and discount-price: ([^"]+) and retail-price: ([^"]+) and discount: ([^"]+)$/
     */
    public function productPricesShouldBe(ProductInterface $product, int $price, $discountPrice, $retailPrice, $discount)
    {
        $this->productShouldBePriced($product, $price);
        $this->productsDiscountPriceShouldBe($product, $discountPrice);
        $this->productsRetailPriceShouldBe($product, $retailPrice);
        $this->productDiscountShouldBe($product, $discount);
    }

    /**
     * @Then /^the (product "[^"]+") should be in (category "[^"]+")$/
     */
    public function theProductShouldBeInCategory(ProductInterface $product, CategoryInterface $category)
    {
        Assert::oneOf($category, $product->getCategories());
    }
}
