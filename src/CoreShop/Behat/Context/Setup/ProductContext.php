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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\ProductStoreValuesInterface;
use CoreShop\Component\Core\Model\ProductUnitDefinitionPriceInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Product\Cloner\ProductQuantityPriceRulesCloner;
use CoreShop\Component\Core\Product\Cloner\ProductUnitDefinitionsCloner;
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
use Pimcore\Model\DataObject\Service;
use Pimcore\Tool;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    private $sharedStorage;
    private $productFactory;
    private $productUnitDefinitions;
    private $productUnitDefinition;
    private $productUnitDefinitionPriceFactory;
    private $unitDefinitionsCloner;
    private $quantityPriceRulesCloner;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $productFactory,
        FactoryInterface $productUnitDefinitions,
        FactoryInterface $productUnitDefinition,
        FactoryInterface $productUnitDefinitionPriceFactory,
        ProductUnitDefinitionsCloner $unitDefinitionsCloner,
        ProductQuantityPriceRulesCloner $quantityPriceRulesCloner
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productFactory = $productFactory;
        $this->productUnitDefinitions = $productUnitDefinitions;
        $this->productUnitDefinition = $productUnitDefinition;
        $this->productUnitDefinitionPriceFactory = $productUnitDefinitionPriceFactory;
        $this->unitDefinitionsCloner = $unitDefinitionsCloner;
        $this->quantityPriceRulesCloner = $quantityPriceRulesCloner;
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
     * @Given /^the site has a product "([^"]+)" priced at (\d+)$/
     * @Given /^the site has a product "([^"]+)" priced at (\d+) for (store "[^"]+")$/
     */
    public function theSiteHasAProductPricedAt(string $productName, int $price = 100, StoreInterface $store = null)
    {
        $product = $this->createProduct($productName, $price, $store);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") is (:?also) priced at (\d+) for (store "[^"]+")$/
     * @Given /^the (product) is priced at (\d+) for (store "[^"]+")$/
     */
    public function theProductIsPriced(ProductInterface $product, int $price, StoreInterface $store)
    {
        $product->setStores(array_merge($product->getStores(), [$store->getId()]));
        $product->setStoreValuesOfType('price', $price, $store);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") has a variant "([^"]+)" priced at ([^"]+)$/
     * @Given /^the (product "[^"]+") has a variant "([^"]+)" priced at ([^"]+) for (store "[^"]+")$/
     * @Given /^the (product) has a variant "([^"]+)" priced at ([^"]+)$/
     */
    public function theProductHasAVariantPricedAt(
        ProductInterface $product,
        string $productName,
        int $price = 100,
        StoreInterface $store = null
    ) {
        $variant = $this->createVariant($product, $productName, $price, $store);

        $this->saveProduct($variant);
    }

    /**
     * @Given /^the (product "[^"]+") has a variant "([^"]+)"$/
     * @Given /^the (product) has a variant "([^"]+)"$/
     */
    public function theProductHasAVariant(
        ProductInterface $product,
        string $productName
    ) {
        $variant = $this->createSimpleVariant($product, $productName);

        $this->saveProduct($variant);
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
    public function theProductWeighsKg(ProductInterface $product, float $kg)
    {
        $product->setWeight($kg);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") measurements are ([^"]+)x([^"]+)x([^"]+)$/
     * @Given /^the (product) measurements are ([^"]+)x([^"]+)x([^"]+)$/
     */
    public function theProductsMeasurementsAre(ProductInterface $product, float $width, float $height, float $depth)
    {
        $product->setWidth($width);
        $product->setHeight($height);
        $product->setDepth($depth);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") is active and published and available for (store "[^"]+")$/
     * @Given /^the (product) is active and published and available for (store "[^"]+")$/
     * @Given /^the (product) is active and published and available$/
     */
    public function theProductIsActivePublishedAndAvailableForStore(ProductInterface $product, StoreInterface $store = null)
    {
        $product->setActive(true);
        $product->setPublished(true);

        if (null !== $store) {
            $product->setStores([$store->getId()]);
        }

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
     * @Given /^the (product "[^"]+") is stock tracked$/
     * @Given /^the (product) is stock tracked$/
     */
    public function theProductIsTracked(ProductInterface $product)
    {
        $product->setIsTracked(true);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") is not stock tracked$/
     * @Given /^the (product) is not stock tracked$/
     */
    public function theProductIsNotTracked(ProductInterface $product)
    {
        $product->setIsTracked(false);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") has (\d+) on hand$/
     * @Given /^the (product) has (\d+) on hand$/
     */
    public function theProductHasOnHand(ProductInterface $product, int $onHand)
    {
        $product->setOnHand($onHand);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") has (\d+) on hold$/
     * @Given /^the (product) has (\d+) on hold$/
     */
    public function theProductHasOnHold(ProductInterface $product, int $onHold)
    {
        $product->setOnHold($onHold);

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
        $product->setStoreValuesOfType('price', $price, $store);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (variant "[^"]+") has a price of ([^"]+) for (store "[^"]+")$/
     * @Given /^the (variants) price is ([^"]+) for (store "[^"]+")$/
     */
    public function theVariantHasAPriceOfForStore(ProductInterface $product, int $price, StoreInterface $store)
    {
        $product->setStoreValuesOfType('price', $price, $store);

        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") has a minimum order quantity of "([^"]+)"$/
     * @Given /^the (product) has a minimum order quantity of "([^"]+)"$/
     */
    public function theProductHasAMinimumOrderQuantity(ProductInterface $product, int $miminumQuantity)
    {
        $product->setMinimumQuantityToOrder($miminumQuantity);
        $this->saveProduct($product);
    }

    /**
     * @Given /^the (product "[^"]+") has a maximum order quantity of "([^"]+)"$/
     * @Given /^the (product) has a maximum order quantity of "([^"]+)"$/
     */
    public function theProductHasAMaximumOrderQuantity(ProductInterface $product, int $maximumQuantity)
    {
        $product->setMaximumQuantityToOrder($maximumQuantity);
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
     * @Given /^the (product) has an additional (unit "[^"]+") with conversion rate ("\d+")$/
     * @Given /^the (product "[^"]+") has an additional (unit "[^"]+") with conversion rate ("\d+")$/
     * @Given /^the (product) has an additional (unit "[^"]+") with conversion rate ("[^"]+") and price (\d+)$/
     * @Given /^the (product) has an additional (unit "[^"]+") with conversion rate ("\d+") and price (\d+) and precision (\d+)$/
     * @Given /^the (product "[^"]+") has an additional (unit "[^"]+") with conversion rate ("\d+") and price (\d+)$/
     * @Given /^the (product "[^"]+") has an additional (unit "[^"]+") with conversion rate ("\d+") and price (\d+) and precision (\d+)$/
     */
    public function theProductHasAnAdditionalUnit(
        ProductInterface $product,
        ProductUnitInterface $unit,
        $conversionRate,
        int $price = null,
        int $precison = 0
    ) {
        $definitions = $this->getOrCreateUnitDefinitions($product->getUnitDefinitions());

        /**
         * @var ProductUnitDefinitionInterface $defaultUnitDefinition
         */
        $defaultUnitDefinition = $this->productUnitDefinition->createNew();
        $defaultUnitDefinition->setUnit($unit);
        $defaultUnitDefinition->setConversionRate((float) $conversionRate);
        $defaultUnitDefinition->setPrecision($precison);

        $definitions->addAdditionalUnitDefinition($defaultUnitDefinition);

        $product->setUnitDefinitions($definitions);

        if (null !== $price) {
            $store = $this->sharedStorage->get('store');

            /**
             * @var ProductUnitDefinitionPriceInterface $productUnitDefinitionPrice
             */
            $productUnitDefinitionPrice = $this->productUnitDefinitionPriceFactory->createNew();
            $productUnitDefinitionPrice->setUnitDefinition($defaultUnitDefinition);
            $productUnitDefinitionPrice->setPrice($price);

            /**
             * @var ProductStoreValuesInterface $storeValues
             */
            $storeValues = $product->getStoreValuesForStore($store);
            $storeValues->addProductUnitDefinitionPrice($productUnitDefinitionPrice);

            $product->setStoreValuesForStore($storeValues, $store);
        }

        $this->saveProduct($product);
    }

    /**
     * @Given /^I copy the (product)$/
     */
    public function iCopyTheProduct(ProductInterface $product)
    {
        $objectService = new Service();
        $newObject = $objectService->copyAsChild($product->getParent(), $product);

        $this->sharedStorage->set('copied-object', $newObject);
    }
    /**
     * @Given /^I copy the (products) unit-definitions and quantity-price-rules to all variants$/
     */
    public function iCopyTheUnitDefinitionsAndQuantityPriceRulesToAllVariants(ProductInterface $product)
    {
        /**
         * @var Concrete $product
         */
        Assert::isInstanceOf($product, Concrete::class);

        foreach ($product->getChildren([AbstractObject::OBJECT_TYPE_VARIANT], true) as $variant) {

            if (!$variant instanceof ProductInterface) {
                continue;
            }

            $this->unitDefinitionsCloner->clone($variant, $product, false);
            $this->quantityPriceRulesCloner->clone($variant, $product, false);
            $variant->save();
        }
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
            $product->setStoreValuesOfType('price', $price, $store);
        }

        return $product;
    }

    /**
     * @param ProductInterface $product
     * @param string           $productName
     *
     * @return ProductInterface
     */
    private function createSimpleVariant(
        ProductInterface $product,
        string $productName
    ) {
        $variant = $this->createSimpleProduct($productName);
        $variant->setParent($product);

        if ($variant instanceof Concrete) {
            $variant->setType(AbstractObject::OBJECT_TYPE_VARIANT);
        }

        return $variant;
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
        $variant = $this->createSimpleVariant($product, $productName);

        if (null === $store && $this->sharedStorage->has('store')) {
            $store = $this->sharedStorage->get('store');
        }

        if (null !== $store) {
            $variant->setStores([$store->getId()]);
            $variant->setStoreValuesOfType('price', $price, $store);
        }

        return $variant;
    }

    /**
     * @param ProductInterface $product
     */
    private function saveProduct(ProductInterface $product)
    {
        $product->save();

        if ($product->getType() === 'variant') {
            $this->sharedStorage->set('variant', $product);
        } else {
            $this->sharedStorage->set('product', $product);
        }
    }
}
