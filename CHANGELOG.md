# Within V2

## 2.0.0-alpha-2 to 2.0.0-alpha-3
 - **BC break** getPrice in PurchasableInterface and ProductInterface has been removed. In favor of this a new coreShopStorePrice has been introduced.
   If you still want to use the old getPrice, create a new Subclass of \CoreShop\Component\Core\Model\Product and implement \CoreShop\Component\Order\Model\PriceAwarePurchasableInterface

# V1 to V2
 - CoreShop 2 is not backward compatible. Due to the framework change, we decided to re-make CoreShop from scratch. If you still have instances running and want to migrate, there is a basic migration way which gets you data from V1 to V2.
 - [Export from CoreShop1](https://github.com/coreshop/CoreShopExport)
 - [Import into CoreShop2](https://github.com/coreshop/ImportBundle)

# Within V1
 - Nothing available