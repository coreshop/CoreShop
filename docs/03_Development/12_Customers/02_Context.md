# Customer Context

CoreShop's approach to customer security leverages
the [Symfony Firewall](https://symfony.com/doc/current/components/security/firewall.html) for authentication purposes.
To enhance flexibility, CoreShop has wrapped this functionality in a context-based system. This setup allows for various
methods of determining customer context.

## Implemented Contexts for Customer Determination

CoreShop includes specific contexts for customer identification, such as:

- **Security Token Based**: A method that uses security tokens for customer identification. More details can be
  found [here](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Customer/Context/RequestBased/TokenBasedRequestResolver.php).

## Creating a Custom Resolver

In scenarios where the default customer context determination method does not fit your requirements, CoreShop allows the
creation of custom resolvers.

### Implementing the Custom Resolver

1. **Interface Implementation**: Your custom resolver should implement
   the `CoreShop\Component\Customer\Context\CustomerContextInterface`.
   The interface includes the `getCustomer` function, which should return
   a `CoreShop\Component\Customer\Model\CustomerInterface` or throw
   a `CoreShop\Component\Customer\Context\CustomerNotFoundException`.

2. **Service Registration**: Register your custom context by using the tag `coreshop.context.customer`. You can also
   specify an optional `priority` attribute for the tag.

```php
// Example PHP code for custom resolver implementation
```

### Use Case for Custom Resolvers

While the need for custom customer context resolvers might be rare, having the ability to create one provides additional
flexibility for specific use cases or unique business requirements.

Implementing a custom resolver allows you to tailor the customer identification process in a way that aligns more
closely with your operational needs or security protocols.
