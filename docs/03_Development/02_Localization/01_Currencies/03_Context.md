# CoreShop Currency Context

For CoreShop to determine the current Currency it uses a concept called Context.

CoreShop comes with a set of default Contexts like:

 - [Storage Based Context](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Currency/Context/StorageBasedCurrencyContext.php)
 - [Store Aware Context](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Currency/Context/StoreAwareCurrencyContext.php)

These Contexts take care about finding the correct Currency for the current Request.

## Create a Custom Resolver

A Currency Context needs to implement the interface ```CoreShop\Component\Currency\Context\CurrencyContextInterface```. This interface
consists of one function called "getCurrency" which returns a ```CoreShop\Component\Currency\Model\CurrencyInterface``` or throws an ```CoreShop\Component\Currency\Context\CurrencyNotFoundException```

To register your context, you need to use the tag: ```coreshop.context.currency``` with an optional ```priority``` attribute.
