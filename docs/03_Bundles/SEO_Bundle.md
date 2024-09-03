# SEO Bundle

The SEO Bundle in CoreShop provides tools and services to enhance the search engine optimization capabilities of your
site. It is designed to work seamlessly with Pimcore's HeadLink and HeadMeta helpers.

## Installation Process

To install the SEO Bundle, use Composer:

```bash
$ composer require coreshop/seo-bundle:^4.0
```

### Activating the Bundle

Enable the bundle inside the kernel or use the Pimcore Extension Manager:

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\SEOBundle\CoreShopSEOBundle(),
    ]);
}
```

## Usage

### Updating SEO Information

Utilize the `coreshop.seo.presentation` service to update SEO metadata:

```php
// From a Controller
$this->get('coreshop.seo.presentation')->updateSeoMetadata($object);
```

### Implementing SEO Interfaces

The SEO Bundle includes several interfaces:

- `CoreShop\Component\SEO\Model\SEOAwareInterface`: For meta-title and meta-description.
- `CoreShop\Component\SEO\Model\SEOImageAwareInterface`: For the og-image attribute.
- `CoreShop\Component\SEO\Model\SEOImageAwareInterface`: For og-title, og-type, and pg-description attributes.

### Implementing SEO Extractors

To create a custom extractor, implement the `ExtractorInterface` and register it with the tag `coreshop.seo.extractor`.

#### Example: Custom Extractor for Product Class

Implement a custom extractor for the Product class:

```php
<?php
// src/App/CoreShop/SEO/Extractor/ProductVideoExtractor.php

namespace App\CoreShop\SEO\Extractor;

//...

final class ProductVideoExtractor implements ExtractorInterface
{
    // Your implementation
}
```

Register the service in your `config/services.yaml`:

```yml
services:
    App\CoreShop\SEO\Extractor\ProductVideoExtractor:
        tags:
            - { name: coreshop.seo.extractor, type: product_video }
```

This bundle simplifies the process of implementing effective SEO strategies, making your CoreShop site more discoverable
and enhancing its online presence.
