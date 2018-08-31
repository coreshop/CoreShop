# CoreShop Currency Context

For CoreShop to determine the current currency it uses a concept called context and context resolver.

## Context

| Name | Priority | Tag | Description|
|------|----------|-----|------------|
| [FixedCurrencyContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Currency/Context/FixedCurrencyContext.php) | default | `coreshop.context.currency` | Used for testing purposes |
| [StorageBasedCurrencyContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Context/Currency/StorageBasedCurrencyContext.php) | default | `coreshop.context.currency` | check if a currency has been changed during a frontend request |
| [CountryAwareCurrencyContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Context/Currency/CountryAwareCurrencyContext.php) | default | `coreshop.context.currency` | Mostly this context will apply since it will get the currency based on the current country context |

These Contexts take care about finding the correct currency for the current request.

## Create a Custom Resolver

A Currency Context needs to implement the interface `CoreShop\Component\Currency\Context\CurrencyContextInterface`.
This interface consists of one method called `getCurrency` which returns a `CoreShop\Component\Currency\Model\CurrencyInterface` or throws an `CoreShop\Component\Currency\Context\CurrencyNotFoundException`

To register your context, you need to use the tag: `coreshop.context.currency` with an optional `priority` attribute.
