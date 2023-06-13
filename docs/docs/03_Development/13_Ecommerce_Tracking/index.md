# CoreShop eCommerce Tracking

CoreShop currently implements Tracking for Analytics and Google Tag Manager.
Tracking is automatically available (but not enabled), as soon as you enable Tag Manager or Analytics in Pimcore.

Per default configuration, all the ecommerce trackers are disabled. You need to enable them manually.

## Available Trackers

 * google-analytics-enhanced-ecommerce
 * google-analytics-universal-ecommerce
 * google-gtag-enhanced-ecommerce
 * google-gtm-classic-ecommerce
 * google-gtm-enhanced-ecommerce
 * matomo (piwik)

## Enabling Trackers

```yml
core_shop_tracking:
    trackers:
        google-analytics-enhanced-ecommerce:
            enabled: false
        google-analytics-universal-ecommerce:
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

### External
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

## Add a custom Tracker
To add a custom tracker you need to implement the interface `CoreShop\Component\Tracking\Tracker\TrackerInterface`

```yaml
app.tracking.tracker.my_tracker:
    class: AppBundle\Tracker\CustomTracker
    parent: coreshop.tracking.tracker.ecommerce_tracker
    tags:
        - { name: coreshop.tracking.tracker, type: app-custom-tracker }
```

## Google Tag Manager
If you have enabled the gtm in backend, CoreShop sends some data to a `dataLayer` object which submits the object to gtm.

### GTM Classic eCommerce
If you enable the classic mode only the order gets submitted if user has successfully reached the "thank-you" page.

### GTM Enhanced eCommerce
There are six Impressions/Events for Google Tag Manager Enhanced eCommerce:

#### Product Impression
**Tag Config Example**:
<pre>
Tag type : Universal Analytics
Track type : Pageview
Enable Enhanced Ecommerce Features: <b>true</b>
Use Data Layer: <b>true</b>
Trigger: event equals <b>gtm.dom</b>
</pre>

#### Product Detail View
**Tag Config Example**:
<pre>
Tag type : Universal Analytics
Track type : Pageview
Enable Enhanced Ecommerce Features: <b>true</b>
Use Data Layer: <b>true</b>
Trigger: event equals <b>gtm.dom</b>
</pre>

#### Checkout Step:
> **Event-Name**: `csCheckout`

**Tag Config Example**:
<pre>
Tag type : Universal Analytics
Track type : Event
Event Category: <b>Ecommerce</b>
Event Action: <b>Checkout</b>
Enable Enhanced Ecommerce Features: <b>true</b>
Use Data Layer: <b>true</b>
Trigger: event equals <b>csCheckout</b>
</pre>

#### Checkout Complete (Purchase):
**Tag Config Example**:
<pre>
Tag type : Universal Analytics
Track type : Pageview
Enable Enhanced Ecommerce Features: <b>true</b>
Use Data Layer: <b>true</b>
Trigger: event equals <b>gtm.dom</b>
</pre>

#### Remove Item from Cart
> **Event-Name**: `csRemoveFromCart`

**Tag Config Example**:
<pre>
Tag type : Universal Analytics
Track type : Event
Event Category: <b>Ecommerce</b>
Event Action: <b>Remove from Cart</b>
Enable Enhanced <b>Ecommerce Features: true</b>
Use Data Layer: <b>true</b>
Trigger: event equals <b>csRemoveFromCart</b>
</pre>

#### Add Item to Cart
> **Event-Name**: `csAddToCart`

**Tag Config Example**:
<pre>
Tag type : Universal Analytics
Track type : Event
Event Category: <b>Ecommerce</b>
Event Action: <b>Add to Cart</b>
Enable Enhanced <b>Ecommerce Features: true</b>
Use Data Layer: <b>true</b>
Trigger: event equals <b>csAddToCart</b>
</pre>
