# CoreShop eCommerce Tracking

CoreShop currently implements Tracking for Analytics and Google Tag Manager. Tracking is automatically enabled, as soon as you enable Tag Manager or Analytics in Pimcore.

Per default configuration, all the ecommerce trackers are disabled. You need to enable them manually.

Available Trackers:

 * google-analytics-enhanced-ecommerce:
 * google-analytics-universal-ecommerce:
 * google-tag-enhanced-ecommerce:
 * piwik:


## Enabling Trackers:

```yml
core_shop_tracking:
    trackers:
        google-analytics-enhanced-ecommerce:
            enabled: true
        google-analytics-universal-ecommerce:
            enabled: true
        google-tag-enhanced-ecommerce:
            enabled: true
        piwik:
            enabled: true
```

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

###### Product Action Add from Cart
```php
$this->get('coreshop.tracking.manager')->trackCartPurchasableAdd($cart, $product);
```

###### Product Action Remove from Cart
```php
$this->get('coreshop.tracking.manager')->trackCartPurchasableRemove($cart, $product);
```

###### Checkout Step
```php
$this->get('coreshop.tracking.manager')->trackCheckoutStep($cart, $stepIdentifier, $isFirstStep, $checkoutOption)
```

###### Checkout Complete
```php
$this->get('coreshop.tracking.manager')->trackCheckoutComplete($order)
```

# Add a new Tracker
To add a new Tracker, extend from `CoreShop\Bundle\TrackingBundle\Tracker\EcommerceTracker`, implement the `CoreShop\Bundle\TrackingBundle\Tracker\EcommerceTrackerInterface` Interface and register your Tracker in the container:

```yaml
app.tracking.tracker.my_tracker:
    class: AppBundle\Tracker\CustomTracker
    parent: coreshop.tracking.tracker.ecommerce_tracker
    calls:
        - [setTracker, ['@app.tracking.my_ecommerce_tracker']]
    tags:
        - { name: coreshop.tracking.tracker, type: app-custom-tracker }
```