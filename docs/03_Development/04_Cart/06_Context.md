# CoreShop Cart Context

For CoreShop to determine the current cart it uses a concept called context and context resolver.

## Context

| Name | Priority | Description|
|------|----------|------------|
| [FixedCartContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Context/FixedCartContext.php) | -100 | Used for testing purposes or for backend order creation |
| [SessionAndStoreBasedCartContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/OrderBundle/Context/SessionAndStoreBasedCartContext.php) | -555 | Search for a valid session cart in given store context |
| [CustomerAndStoreBasedCartContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/OrderBundle/Context/CustomerAndStoreBasedCartContext.php) | -777 | Search for a cart based on a customer. **Note**: This context only triggers after a user has been successfully logged in. It searches for the last available cart a user may has left. |
| [CartContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Context/CartContext.php) | -999 | If all other context classes failed finally this context will create a fresh cart |


## Create a Custom Resolver

To register your context, you need to use the tag: `coreshop.context.cart` with an optional `priority` attribute.
