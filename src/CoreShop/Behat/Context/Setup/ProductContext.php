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
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Pimcore\File;
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
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $productFactory
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $productFactory,
        ProductRepositoryInterface $productRepository
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * @Given /^the site has a product "([^"]+)" priced at ([^"]+)$/
     */
    public function theSiteHasAProductPricedAt(string $productName, int $price = 100, StoreInterface $store = null)
    {
        $product = $this->createProduct($productName, $price, $store);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") is in (category "[^"]+")$/
     * @Given /^([^"]+) is in (category "[^"]+")$/
     */
    public function theProductIsInCategory(ProductInterface $product, CategoryInterface $category)
    {
        $product->setCategories([$category]);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") has (tax rule group "[^"]+")$/
     * @Given /^([^"]+) has the (tax rule group "[^"]+")$/
     */
    public function theProductHasTaxRuleGroup(ProductInterface $product, TaxRuleGroupInterface $taxRuleGroup)
    {
        $product->setTaxRule($taxRuleGroup);

        $this->saveProduct($product);
    }

    /**
     * @param string $productName
     * @param int $price
     * @param StoreInterface|null $store
     *
     * @return ProductInterface
     */
    private function createProduct(string $productName, int $price = 100, StoreInterface $store = null)
    {
        if (null === $store && $this->sharedStorage->has('store')) {
            $store = $this->sharedStorage->get('store');
        }

        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();

        $product->setKey(File::getValidFilename($productName));
        $product->setParent(Folder::getByPath('/'));
        $product->setName($productName, 'en');

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
