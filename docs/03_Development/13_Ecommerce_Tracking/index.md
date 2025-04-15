# eCommerce Tracking

CoreShop implements tracking for analytics and Google Tag Manager, integrating seamlessly as soon as Tag Manager or
Analytics is enabled in Pimcore. By default, all ecommerce trackers are disabled and need to be manually activated.

## Available Trackers

CoreShop supports a variety of trackers, including:

- google-analytics-enhanced-ecommerce
- google-analytics-universal-ecommerce
- google-gtag-enhanced-ecommerce
- google-gtm-classic-ecommerce
- google-gtm-enhanced-ecommerce
- matomo (piwik)

## Enabling Trackers

To enable a specific tracker, modify the configuration as follows:

```yml
core_shop_tracking:
   trackers:
      google-analytics-enhanced-ecommerce:
        enabled: false
      google-analytics-4:
        enabled: false
      google-gtag-enhanced-ecommerce:
        enabled: false
      google-gtm-classic-ecommerce:
        enabled: false
      google-gtm-enhanced-ecommerce:
        enabled: false
      matomo:
        enabled: true
```

### External Resources

For more information on these tracking methods:

- [Google Enhanced E-Commerce](https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce)
- [Google Enhanced E-Commerce with gtag.js](https://developers.google.com/analytics/devguides/collection/gtagjs/enhanced-ecommerce)
- [Google Tag Manager Enhanced E-Commerce](https://developers.google.com/tag-manager/enhanced-ecommerce)
- [Google Tag Manager Classic E-Commerce](https://support.google.com/tagmanager/answer/6107169?hl=en)
- [Matomo (Piwik) E-Commerce](https://matomo.org/docs/ecommerce-analytics/)

#### Actions

##### Product Impression

```php
$this->get('coreshop.tracking.manager')->trackProductImpression($product);
```

##### Product View

```php
$this->get('coreshop.tracking.manager')->trackProduct($product);
```

##### Product Action Add from Cart

```php
$this->get('coreshop.tracking.manager')->trackCartAdd($cart, $product);
```

##### Product Action Remove from Cart

```php
$this->get('coreshop.tracking.manager')->trackCartRemove($cart, $product);
```

##### Checkout Step

```php
$this->get('coreshop.tracking.manager')->trackCheckoutStep($cart, $stepIdentifier, $isFirstStep, $checkoutOption)
```

##### Checkout Complete

```php
$this->get('coreshop.tracking.manager')->trackCheckoutComplete($order)
```

## Adding a Custom Tracker

To add a custom tracker:

1. Implement the interface `CoreShop\Component\Tracking\Tracker\TrackerInterface`.

2. Register the tracker as a service:

```yaml
App\CoreShop\Tracker\CustomTracker:
  parent: coreshop.tracking.tracker.ecommerce_tracker
  tags:
    - { name: coreshop.tracking.tracker, type: app-custom-tracker }
```

## Google Tag Manager

CoreShop sends data to a `dataLayer` object for Google Tag Manager, which then submits the object to GTM.

### GTM Classic eCommerce

In classic mode, only the order gets submitted when the user reaches the "thank-you" page.

### GTM Enhanced eCommerce

For Google Tag Manager Enhanced eCommerce, there are six Impressions/Events:

- Product Impression
- Product Detail View
- Checkout Step
- Checkout Complete (Purchase)
- Remove Item from Cart
- Add Item to Cart

Each impression/event has specific configurations for GTM tagging, as outlined in the detailed tag config examples
provided.