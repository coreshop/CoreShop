# Within V2

## 2.0.0-alpha-3 to 2.0.0-alpha-4
 - **BC break** decoupled MoneyBundle from CurrencyBundle, therefore the Twig Extension for Money Conversion went to the CurrencyBundle. Therefore the name of that extension was renamed from
   **coreshop_convert_money** to **coreshop_convert_currency**. If you use it directly in your code, please rename all of them.
 - Deprecated CoreShopAdminBundle which responsability was only handling installation and deliviering pimcore resources like JS and CSS files.
    * Installation has been moved to CoreShopCoreBundle
    * Delivering of resources is now handled by CoreShopResourceBundle, this also makes it easier to use CoreShop Bundles without handling resources yourself.

## 2.0.0-alpha-2 to 2.0.0-alpha-3
 - **BC break** getPrice in PurchasableInterface and ProductInterface has been removed. In favor of this a new coreShopStorePrice editable has been introduced, which stores prices for each store. This makes handling of multiple currencies way more elegant.
 
   If you still want to use the old getPrice, create a new Subclass of \CoreShop\Component\Core\Model\Product, implement \CoreShop\Component\Order\Model\PriceAwarePurchasableInterface and set your class to CoreShopProduct parents.

# V1 to V2
 - CoreShop 2 is not backward compatible. Due to the framework change, we decided to re-make CoreShop from scratch. If you still have instances running and want to migrate, there is a basic migration way which gets you data from V1 to V2.
 - [Export from CoreShop1](https://github.com/coreshop/CoreShopExport)
 - [Import into CoreShop2](https://github.com/coreshop/ImportBundle)

# Within V1
 - Nothing available