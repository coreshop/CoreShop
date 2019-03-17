<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Product\Model\ManufacturerInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionsInterface;
use CoreShop\Component\Product\Model\ProductUnitInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Model\AbstractObject;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use Pimcore\File;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Tool;

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
     * @var FactoryInterface
     */
    private $productUnitDefinitions;

    /**
     * @var FactoryInterface
     */
    private $productUnitDefinition;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface       $productFactory
     * @param FactoryInterface       $productUnitDefinitions
     * @param FactoryInterface       $productUnitDefinition
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $productFactory,
        FactoryInterface       $productUnitDefinitions,
        FactoryInterface       $productUnitDefinition
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productFactory = $productFactory;
        $this->productUnitDefinitions = $productUnitDefinitions;
        $this->productUnitDefinition = $productUnitDefinition;
    }

    /**
     * @Given /^the site has a product "([^"]+)"$/
     */
    public function theSiteHasAProduct(string $productName)
    {
        $product = $this->createSimpleProduct($productName);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") has a meta title "([^"]+)"$/
     * @Given /^the (products) meta title is "([^"]+)"$/
     */
    public function theProductHasAMetaTitle(ProductInterface $product, $metaTitle)
    {
        $product->setPimcoreMetaTitle($metaTitle);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") has a meta description "([^"]+)"$/
     * @Given /^the (products) meta description is "([^"]+)"$/
     */
    public function theProductHasAMetaDescription(ProductInterface $product, $metaDescription)
    {
        $product->setPimcoreMetaDescription($metaDescription);

        $this->saveProduct($product);
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
     * @Given /^the (product "[^"]+") has a variant "([^"]+)" priced at ([^"]+)$/
     */
    public function theProductHasAVariant(
        ProductInterface $product,
        string $productName,
        int $price = 100,
        StoreInterface $store = null
    ) {
        $variant = $this->createVariant($product, $productName, $price, $store);

        $this->saveVariant($variant);
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
     * @Given /^the (product) has the (tax rule group "[^"]+")$/
     */
    public function theProductHasTaxRuleGroup(ProductInterface $product, TaxRuleGroupInterface $taxRuleGroup)
    {
        $product->setTaxRule($taxRuleGroup);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") host description is "([^"]+)"$/
     * @Given /^the (products) short description is "([^"]+)"$/
     */
    public function theProductHasAShortDescription(ProductInterface $product, $description)
    {
        $product->setShortDescription($description);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") weighs ([^"]+)kg$/
     * @Given /^the (product) weighs ([^"]+)kg$/
     */
    public function theProductWeighsKg(ProductInterface $product, $kg)
    {
        $product->setWeight($kg);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") measurements are ([^"]+)x([^"]+)x([^"]+)$/
     * @Given /^the (product) measurements are ([^"]+)x([^"]+)x([^"]+)$/
     */
    public function theProductsMeasurementsAre(ProductInterface $product, $width, $height, $depth)
    {
        $product->setWidth($width);
        $product->setHeight($height);
        $product->setDepth($depth);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") ean is "([^"]+)"$/
     * @Given /^the (products) ean is "([^"]+)"$/
     */
    public function theProductsEanIs(ProductInterface $product, $ean)
    {
        $product->setEan($ean);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") is active$/
     * @Given /^the (product) is active$/
     */
    public function theProductIsActive(ProductInterface $product)
    {
        $product->setActive(true);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") is not active$/
     * @Given /^the (product) is not active$/
     */
    public function theProductIsNotActive(ProductInterface $product)
    {
        $product->setActive(false);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") is published$/
     * @Given /^the (product) is published$/
     */
    public function theProductIsPublished(ProductInterface $product)
    {
        $product->setPublished(true);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") is not published$/
     * @Given /^the (product) is not published$/
     */
    public function theProductIsNotPublished(ProductInterface $product)
    {
        $product->setPublished(false);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") sku is "([^"]+)"$/
     * @Given /^the (products) sku is "([^"]+)"$/
     */
    public function theProductsSkuIs(ProductInterface $product, $sku)
    {
        $product->setSku($sku);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") has (manufacturer "[^"]+")$/
     * @Given /^the (products) has (manufacturer "[^"]+")$/
     */
    public function theProductHasManufacturer(ProductInterface $product, ManufacturerInterface $manufacturer)
    {
        $product->setManufacturer($manufacturer);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") has a price of ([^"]+) for (store "[^"]+")$/
     * @Given /^the (products) price is ([^"]+) for (store "[^"]+")$/
     */
    public function theProductHasAPriceOfForStore(ProductInterface $product, int $price, StoreInterface $store)
    {
        $product->setStorePrice($price, $store);

        $this->saveProduct($product);
    }


    /**
     * @Given /^the (product) has the default (unit "[^"]+")$/
     * @Given /^the (product "[^"]+") has the default (unit "[^"]+")"$/
     */
    public function theProductHasTheDefaultUnit(ProductInterface $product, ProductUnitInterface $unit)
    {
        $definitions = $this->getOrCreateUnitDefinitions($product->getUnitDefinitions());

        /**
         * @var ProductUnitDefinitionInterface $defaultUnitDefinition
         */
        $defaultUnitDefinition = $this->productUnitDefinition->createNew();
        $defaultUnitDefinition->setUnit($unit);

        $definitions->setDefaultUnitDefinition($defaultUnitDefinition);

        $product->setUnitDefinitions($definitions);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product) has and additional (unit "[^"]+") with conversion rate ("[^"]+")$/
     * @Given /^the (product "[^"]+") has and additional (unit "[^"]+") with conversion rate ("[^"]+")$/
     */
    public function theProductHasAnAdditionalUnit(ProductInterface $product, ProductUnitInterface $unit, $conversionRate)
    {
        $definitions = $this->getOrCreateUnitDefinitions($product->getUnitDefinitions());

        /**
         * @var ProductUnitDefinitionInterface $defaultUnitDefinition
         */
        $defaultUnitDefinition = $this->productUnitDefinition->createNew();
        $defaultUnitDefinition->setUnit($unit);
        $defaultUnitDefinition->setConversionRate((float)$conversionRate);

        $definitions->addAdditionalUnitDefinition($defaultUnitDefinition);

        $product->setUnitDefinitions($definitions);

        $this->saveProduct($product);
    }

    private function getOrCreateUnitDefinitions(ProductUnitDefinitionsInterface $definitions = null)
    {
        if (null === $definitions) {
            $definitions = $this->productUnitDefinitions->createNew();
        }

        return $definitions;
    }

    /**
     * @param string $productName
     *
     * @return ProductInterface
     */
    private function createSimpleProduct(string $productName)
    {
        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();

        $product->setKey(File::getValidFilename($productName));
        $product->setParent(Folder::getByPath('/'));

        foreach (Tool::getValidLanguages() as $lang) {
            $product->setName($productName, $lang);
        }

        return $product;
    }

    /**
     * @param string              $productName
     * @param int                 $price
     * @param StoreInterface|null $store
     *
     * @return ProductInterface
     */
    private function createProduct(string $productName, int $price = 100, StoreInterface $store = null)
    {
        /** @var ProductInterface $product */
        $product = $this->createSimpleProduct($productName);

        if (null === $store && $this->sharedStorage->has('store')) {
            $store = $this->sharedStorage->get('store');
        }

        if (null !== $store) {
            $product->setStores([$store->getId()]);
            $product->setStorePrice($price, $store);
        }

        return $product;
    }

    /**
     * @param ProductInterface    $product
     * @param string              $productName
     * @param int                 $price
     * @param StoreInterface|null $store
     *
     * @return ProductInterface
     */
    private function createVariant(
        ProductInterface $product,
        string $productName,
        int $price = 100,
        StoreInterface $store = null
    ) {
        $variant = $this->createSimpleProduct($productName);
        $variant->setParent($product);

        if ($variant instanceof Concrete) {
            $variant->setType(AbstractObject::OBJECT_TYPE_VARIANT);
        }

        if (null === $store && $this->sharedStorage->has('store')) {
            $store = $this->sharedStorage->get('store');
        }

        if (null !== $store) {
            $variant->setStores([$store->getId()]);
            $variant->setStorePrice($price, $store);
        }

        return $variant;
    }

    /**
     * @param ProductInterface $product
     */
    private function saveProduct(ProductInterface $product)
    {
        $product->save();
        $this->sharedStorage->set('product', $product);
    }

    /**
     * @param ProductInterface $product
     */
    private function saveVariant(ProductInterface $product)
    {
        $product->save();
        $this->sharedStorage->set('variant', $product);
    }
}
