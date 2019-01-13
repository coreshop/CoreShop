# CoreShop Store Front Controller

If you use CoreShop FrontendBundle, you can change the Controllers it uses. Simply change the configuration for the controller:

```yaml
core_shop_frontend:
    controllers:
        index: CoreShop\Bundle\FrontendBundle\Controller\IndexController
        register: CoreShop\Bundle\FrontendBundle\Controller\RegisterController
        customer: CoreShop\Bundle\FrontendBundle\Controller\CustomerController
        currency: CoreShop\Bundle\FrontendBundle\Controller\CurrencyController
        language: CoreShop\Bundle\FrontendBundle\Controller\LanguageController
        search: CoreShop\Bundle\FrontendBundle\Controller\SearchController
        cart: CoreShop\Bundle\FrontendBundle\Controller\CartController
        checkout: CoreShop\Bundle\FrontendBundle\Controller\CheckoutController
        category: CoreShop\Bundle\FrontendBundle\Controller\CategoryController
        product: CoreShop\Bundle\FrontendBundle\Controller\ProductController
        quote: CoreShop\Bundle\FrontendBundle\Controller\QuoteController
        security: CoreShop\Bundle\FrontendBundle\Controller\SecurityController
        payment: CoreShop\Bundle\PayumBundle\Controller\PaymentController
```

## Example of using a Custom ProductController

**1**: Add a new Controller and inherit from the FrontendController

```php
<?php

namespace AppBundle\Controller;

use CoreShop\Component\Core\Model\ProductInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends \CoreShop\Bundle\FrontendBundle\Controller\ProductController
{
    public function detailAction(Request $request)
    {
        //Do whatever you want in here

        return parent::detailAction($request);
    }
}
```

**2**: Change Configuration of the Controller:

```yaml
core_shop_frontend:
    controllers:
        product: AppBundle\Controller\ProductController
```

**3**: CoreShop Override Controller (optional)

CoreShop uses services for controllers, if you need to extend a controller, simply override the service:

```yaml
services:
    coreshop.frontend.controller.category:
        class: AppBundle\Controller\ProductController
        parent: coreshop.frontend.controller.abstract
        public: true
        autowire: true
        autoconfigure: false
```
