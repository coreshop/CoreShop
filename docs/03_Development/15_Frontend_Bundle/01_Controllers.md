# Frontend Bundle Controllers

CoreShop's FrontendBundle allows customization of its controllers. You can modify the controllers used by changing the
configuration settings in your application.

## Configuring Frontend Controllers

To specify which controllers to use, adjust the `core_shop_frontend` configuration in your `yaml` file:

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

## Implementing a Custom ProductController

### Step 1: Create a New Controller

Create a new controller by inheriting from the CoreShop FrontendController:

```php
<?php

namespace App\CoreShop\Controller;

use CoreShop\Component\Core\Model\ProductInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends \CoreShop\Bundle\FrontendBundle\Controller\ProductController
{
    public function detailAction(Request $request)
    {
        // Customize your functionality here

        return parent::detailAction($request);
    }
}
```

### Step 2: Update Configuration

After creating your custom controller, update the configuration to use it:

```yaml
core_shop_frontend:
  controllers:
    product: App\CoreShop\Controller\ProductController
```

By following these steps, you can effectively customize and extend the functionality of CoreShop's FrontendBundle
controllers to meet your specific requirements.
