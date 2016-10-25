# CoreShop Tracking

# Tracking Configuration

To enable different kinds of tracker, CoreShop uses DI to configure them

```php
'coreshop.tracker' => [
    DI\object('CoreShop\Tracking\Analytics\EnhancedEcommerce')
]
```

# Overview
##### External
[Google Documentation Enhanced E-Commerce](https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce)

#### Actions

###### Product Impression        
```php
\CoreShop\Tracking\TrackingManager::getInstance()->trackProductImpression($product)
```

###### Product View
```php
\CoreShop\Tracking\TrackingManager::getInstance()->trackProductView($product)
```

###### Product Action Add
```php
\CoreShop\Tracking\TrackingManager::getInstance()->trackProductActionAdd($product, $quantity)
```

###### Product Action Remove
```php
\CoreShop\Tracking\TrackingManager::getInstance()->trackProductActionRemove($product, $quantity)
```

###### Checkout
```php
\CoreShop\Tracking\TrackingManager::getInstance()->trackCheckout($cart)
```

###### Checkout Complete
```php
\CoreShop\Tracking\TrackingManager::getInstance()->trackCheckoutComplete($order)
```

###### Checkout Step
```php
\CoreShop\Tracking\TrackingManager::getInstance()->trackCheckoutStep($step, $cart, $stepNumber, $checkoutOption)
```

# Usage
#### Tracking Actions

CoreShop already calls all necessary Tracking Actions in its Controllers. Except for Cart-Add and Cart-Remove. The problem with them is, they use ajax to refresh the UI, so no View Rendering happens there. 

```php
\CoreShop\Tool::loadController("Product");

class ProductController extends CoreShop_ProductController {
    public function detailAction()
    {
         parent::detailAction();

         \CoreShop\Tracking\TrackingManager::getInstance()->trackProductView($this->view->product);
    }
}


```