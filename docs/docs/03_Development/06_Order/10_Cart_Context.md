# Cart Context

CoreShop uses a system of context and context resolvers to determine the current cart.

The Cart Context adheres to the
interface [```CoreShop\Component\Order\Context\CartContextInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Context/CartContextInterface.php)
and is implemented in the service `coreshop.context.cart`.

## Getting the Current Cart

To obtain the current cart, use the `coreshop.context.cart` service:

```php
$cartContext = $container->get('coreshop.context.cart');

// Get current cart, if none exists, a new one is created
$cart = $cartContext->getCart();
```

## Context Types

The table below outlines the various contexts used for cart determination:

| Name                                                                                                                                                              | Priority | Description                                                                                      |
|-------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------|--------------------------------------------------------------------------------------------------|
| [FixedCartContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Context/FixedCartContext.php)                                    | -100     | For testing or backend order creation.                                                           |
| [SessionAndStoreBasedCartContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/OrderBundle/Context/SessionAndStoreBasedCartContext.php)   | -555     | Searches for a valid session cart in the given store context.                                    |
| [CustomerAndStoreBasedCartContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/OrderBundle/Context/CustomerAndStoreBasedCartContext.php) | -777     | Searches for a cart based on a customer, activates post-login to find the last cart a user left. |
| [CartContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Context/CartContext.php)                                              | -999     | Creates a new cart if other contexts fail.                                                       |

## Create a Custom Resolver

To register a custom context, use the tag `coreshop.context.cart` with an optional `priority` attribute.
