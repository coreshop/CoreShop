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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
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
     * @var FactoryInterface
     */
    private $productFactory;

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
     * @param FactoryInterface $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ProductPriceCalculatorInterface $productPriceCalculator
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $productFactory,
        ProductRepositoryInterface $productRepository,
        ProductPriceCalculatorInterface $productPriceCalculator
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->productPriceCalculator = $productPriceCalculator;
    }

    /**
     * @Given /^the site has a product "([^"]+)" priced at ([^"]+)$/
     */
    public function storeHasAProductPricedAt($productName, $price = 100, StoreInterface $store = null)
    {
        $product = $this->createProduct($productName, $price, $store);

        $this->saveProduct($product);
    }

    /**
     * @Then Product should be priced :price
     */
    public function productShouldBePriced(int $price)
    {
        Assert::same(intval($price), $this->productPriceCalculator->getPrice($this->sharedStorage->get('product')));
    }

    /**
     * @param string $productName
     * @param int $price
     * @param StoreInterface|null $store
     *
     * @return ProductInterface
     */
    private function createProduct($productName, $price = 100, StoreInterface $store = null)
    {
        if (null === $store && $this->sharedStorage->has('store')) {
            $store = $this->sharedStorage->get('store');
        }

        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();

        $product->setKey($productName);
        $product->setParent(Folder::getByPath('/'));
        $product->setName($productName);

        if (null !== $store) {
            $product->setStores([$store->getId()]);
            $product->setStorePrice($price, $store);
        }

        return $product;
    }

    /**
     * @param ProductInterface $product
     */
    private function saveProduct(ProductInterface $product)
    {
        $product->save();
        $this->sharedStorage->set('product', $product);
    }
}
