# CoreShop SEO Bundle

## Installation
```bash
$ composer require coreshop/seo-bundle:^2.0
```

### Activating Bundle
You need to enable the bundle inside the kernel or with the Pimcore Extension Manager.

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\SEOBundle\CoreShopSEOBundle()
    ]);
}
```

## Usage

The SEO Bundle provides you with services to make SEO more manageable. It depends on Pimcore's HeadLink and HeadMeta Helper.

There are multiple ways of making use of this bundle:

 - Implement SEO interfaces provided with this bundle
 - Implement Custom Extractors and add them to the SEOMetadata Model

To update the SEO Information, you need to use the service ```coreshop.seo.presentation```:

```php

//From a Controller
$this->get('coreshop.seo.presentation')->updateSeoMetadata($object);
```

### Implement SEO Interfaces
SEO Bundle comes with 3 SEO Aware interfaces you can take advantage of:

 - `CoreShop\Component\SEO\Model\SEOAwareInterface` for meta-title and meta-description
 - `CoreShop\Component\SEO\Model\SEOImageAwareInterface` for og-image attribute
 - `CoreShop\Component\SEO\Model\SEOImageAwareInterface` for og-title, og-type and pg-description attribute

### Implement SEO Extractors
To add a new Extractor, you need to implement the interface ```CoreShop\Component\SEO\Extractor\ExtractorInterface``` and register your class with the tag ```coreshop.seo.extractor```:

#### Example
Let's implement a custom extractor for our Product class with a Video.


```php
<?php
//src/AppBundle/SEO/Extractor/ProductVideoExtractor.php

namespace AppBundle\SEO\Extractor;

use Pimcore\Model\DataObject\Product;
use Pimcore\Tool;

final class ProductVideoExtractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($object)
    {
        return $object instanceof Product && $object->getVideoUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function updateMetadata($object, SEOMetadataInterface $seoMetadata)
    {
        /**
         * @var $object Product
         */
        $seoMetadata->addExtraProperty('og:video', Tool::getHostUrl() . $object->getVideoUrl());
    }
}
```

Now we need to register the service

```yml
# src/AppBundle/Resources/config/services.yml
services:
    AppBundle\SEO\Extractor:
        tags:
            - { name: coreshop.seo.extractor, type: product_video }

```