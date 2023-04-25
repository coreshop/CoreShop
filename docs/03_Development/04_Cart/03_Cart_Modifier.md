# CoreShop Cart Modifier

CoreShop provides you with a helper service to modify the cart. It handles following for you:

 - adding items
 - removing items
 - change quantity of items

The Modifier implements the interface [```CoreShop\Component\Order\Cart\CartModifierInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Cart/CartModifierInterface.php) and is implemented by the service
```coreshop.cart.modifier```

The Cart Modifier itself, uses the [Storage List Component](../../03_Bundles/StorageList_Bundle.md)