# CoreShop Tracking
# TODO

# Tracking Configuration

CoreShop currently implements Tracking for Analytics and Google Tag Manager. Tracking is automatically enabled, as soon as you enable Tag Manager or Analytics in Pimcore.

# Overview
##### External
[Google Documentation Enhanced E-Commerce](https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce)

#### Actions

###### Product Impression        
```php
$this->get('coreshop.tracking.manager')->trackPurchasableImpression($product);
```

###### Product View
```php
$this->get('coreshop.tracking.manager')->trackPurchasableView($product);
```

###### Product Action Add
```php
$this->get('coreshop.tracking.manager')->trackPurchasableActionAdd($product);
```

###### Product Action Remove
```php
$this->get('coreshop.tracking.manager')->trackPurchasableActionRemove($product);
```

###### Checkout
```php
$this->get('coreshop.tracking.manager')->trackCheckout($cart);
```

###### Checkout Complete
```php
$this->get('coreshop.tracking.manager')->trackCheckoutComplete($order)
```

###### Checkout Step
```php
$this->get('coreshop.tracking.manager')->trackCheckoutStep($step, $cart, $stepNumber, $checkoutOption)
```

# Add a new Tracker
To add a new Tracker, implement the Interface: CoreShop\Bundle\TrackingBundle\TrackerInterface and register your Tracker in the container:

```yml
app.tracking.tracker.my_tracker:
    class: AppBundle\Tracker\CustomTracker
    tags:
      - { name: coreshop.tracking.tracker, type: app-custom-tracker }
```