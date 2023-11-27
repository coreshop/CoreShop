# Cart Modifier

CoreShop includes a helper service, known as the Cart Modifier, to facilitate modifications to the cart. This service
simplifies tasks such as:

- Adding items to the cart
- Removing items from the cart
- Changing the quantity of items in the cart

The Cart Modifier adheres to the
interface [`CoreShop\Component\Order\Cart\CartModifierInterface`](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Cart/CartModifierInterface.php)
and is implemented by the service `coreshop.cart.modifier`.
