# CoreShop Cart Context

For CoreShop to determine the current cart it uses a concept called context and context resolver.

The Cart Context implements the Interface [```CoreShop\Component\Order\Context\CartContextInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Context/CartContextInterface.php) and is implemented in the Service
```coreshop.context.cart```:

## Getting the current Cart

If you want to get the current cart, you simply use the service:

```php
$cartContext = $container->get('coreshop.context.cart');

// Get current cart, if none exists, it creates a new one
$cart = $cartContext->getCart();

```

## Context

| Name | Priority | Description|
|------|----------|------------|
| [FixedCartContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Context/FixedCartContext.php) | -100 | Used for testing purposes or for backend order creation |
| [SessionAndStoreBasedCartContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/OrderBundle/Context/SessionAndStoreBasedCartContext.php) | -555 | Search for a valid session cart in given store context |
| [CartContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Context/CartContext.php) | -999 | If all other context classes failed finally this context will create a fresh cart |


## Create a Custom Resolver

To register your context, you need to use the tag: `coreshop.context.cart` with an optional `priority` attribute.
