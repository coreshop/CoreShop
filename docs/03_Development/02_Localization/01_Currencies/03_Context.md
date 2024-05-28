# Currency Context

CoreShop utilizes a concept known as context and context resolvers to determine the current currency. This section
explains the different types of contexts available and how to create a custom resolver.

## Context Types

CoreShop defines several contexts to identify the appropriate currency for the current request. Each context is
characterized by its name, priority, associated tag, and a specific role. The following table outlines these contexts:

| Name                                                                                                                                                         | Priority | Tag                         | Description                                                                       |
|--------------------------------------------------------------------------------------------------------------------------------------------------------------|----------|-----------------------------|-----------------------------------------------------------------------------------|
| [FixedCurrencyContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Currency/Context/FixedCurrencyContext.php)                    | default  | `coreshop.context.currency` | Primarily used for testing purposes.                                              |
| [StorageBasedCurrencyContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Context/Currency/StorageBasedCurrencyContext.php) | default  | `coreshop.context.currency` | Checks if a currency has been changed during a frontend request.                  |
| [CountryAwareCurrencyContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Context/Currency/CountryAwareCurrencyContext.php) | default  | `coreshop.context.currency` | Commonly used as it determines the currency based on the current country context. |

## Creating a Custom Currency Resolver

To implement a custom Currency Context, follow these steps:

1. **Implement the Interface**: Your custom context should implement
   the `CoreShop\Component\Currency\Context\CurrencyContextInterface`.
2. **Define the Method**: The interface requires the implementation of a method named `getCurrency`. This method should
   return an instance of `CoreShop\Component\Currency\Model\CurrencyInterface` or throw
   a `CoreShop\Component\Currency\Context\CurrencyNotFoundException` if the currency cannot be determined.
3. **Register the Context**: Add your custom context to the system by using the tag `coreshop.context.currency`. You can
   also assign a `priority` attribute, which is optional but can be used to define the order in which contexts are
   evaluated.
