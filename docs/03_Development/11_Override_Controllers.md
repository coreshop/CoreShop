# CoreShop Override Controller

CoreShop implements a default behavior for Checkout, Cart, User, etc. If you need to change some of this behavior, you can override the Controller using your Website.

For example we would like to override the CartController. Simply create the file "website/controllers/CartController.php" with following code:

```php

\CoreShop\Bundle\LegacyBundle\Tool::loadController("Cart");

class CartController extends CoreShop_CartController {

    public function init()
    {
        parent::init();
    }
}

```
