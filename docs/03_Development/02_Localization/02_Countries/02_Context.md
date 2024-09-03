# Country Context

CoreShop uses a system of contexts and resolvers to determine the current country of a visitor or customer.

## Context

Different contexts are used for determining the appropriate country in various scenarios:

| Name                                                                                                                                                  | Priority | Tag                        | Description                                                   |
|-------------------------------------------------------------------------------------------------------------------------------------------------------|----------|----------------------------|---------------------------------------------------------------|
| [FixedCountryContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Address/Context/FixedCountryContext.php)                | default  | `coreshop.context.country` | For testing purposes.                                         |
| [CountryContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Address/Context/RequestBased/CountryContext.php)             | default  | `coreshop.context.country` | Checks for a country within the country request resolver.     |
| [StoreAwareCountryContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Context/Country/StoreAwareCountryContext.php) | default  | `coreshop.context.country` | Considers the current store context to determine the country. |

## Resolver

Resolvers are used to find the correct country based on different criteria:

| Name                                                                                                                                                                | Priority | Tag                                               | Description                                            |
|---------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------|---------------------------------------------------|--------------------------------------------------------|
| [GeoLiteBasedRequestResolver](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Address/Context/RequestBased/GeoLiteBasedRequestResolver.php) | 10       | `coreshop.context.country.request_based.resolver` | Uses the Geo Lite Database to determine user location. |

## Create a Custom Resolver

To create a custom Country Context:

1. **Implement the Interface**: Your context should
   implement `CoreShop\Component\Address\Context\CountryContextInterface`.

2. **Define the Method**: This interface has a method `getCountry`
   returning `CoreShop\Component\Address\Model\CountryInterface` or
   throwing `CoreShop\Component\Address\Context\CountryNotFoundException`.

3. **Register the Context**: Use the tag `coreshop.context.country` with an optional `priority` attribute for
   registration.

## Create a Request-Based Resolver

CoreShop supports custom request-based country context resolvers:

1. **Implement the Interface**: Create a resolver that
   implements `CoreShop\Component\Address\Context\RequestBased\RequestResolverInterface`.

2. **Define the Method**: Implement the method `findCountry`, which returns a country or null based on the request.

3. **Register the Resolver**: Use the tag `coreshop.context.country.request_based.resolver` with an optional `priority`
   attribute.

## Example

Creating a `DocumentBasedRequestRequestResolver` based on Pimcore Document:

```php
// PHP code for creating DocumentBasedRequestRequestResolver
```

And configure the service:

```yaml
services:
   App\CoreShop\Address\Context\DocumentBasedRequestRequestResolver:
      arguments:
        - '@Pimcore\Http\Request\Resolver\DocumentResolver'
        - '@coreshop.repository.country'
      tags:
        - { name: coreshop.context.country.request_based.resolver }
```

CoreShop will now resolve the current country based on the Pimcore site being accessed.
