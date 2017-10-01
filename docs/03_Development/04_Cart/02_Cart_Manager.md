# CoreShop Cart Manager

The Cart Manager helps you managing Carts. It handles:

 - Multi Cart
 - Session Cart
 - Persisting Carts
 - Delete Cart

The Cart Manager implements the Interface ```CoreShop\Component\Order\Manager\CartManagerInterface``` and is implemented in the Service
```coreshop.cart.manager```:

## Getting the current Cart

If you want to get the current cart, you simply use the service:

```
$cartManager = $container->get('coreshop.cart.manager');

// Get current cart, if none exists, it creates a new one
$cart = $cartManager->getCart();

```