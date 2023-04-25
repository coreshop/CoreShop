# CoreShop Variant Bundle

CoreShop Variant Bundle allows you to manage different Variants of a Product.

## Installation
```bash
  composer require coreshop/variant-bundle
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel.

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\VariantBundle\CoreShopVariantBundle(),
    ]);
}
```

## Abstract
Pimcore already supports variants. But it doesn't define what a Variant is, or how a variant differs from the Parent Product or other Variants.

In classical e-commerce scenarios, you usually have different Variation Types like Size and Color with different Values like `XL` or `Red`.

CoreShop describes these two types of entities as `Group` (`CoreShop\Component\Variant\Model\AttributeGroupInterface`) and `Value` (`CoreShop\Component\Variant\Model\AttributeValueInterface`

The "Product" itself than is a sort-of abstract entity that is used to define what Attribute Groups are allowed. The Pimcore Variants then, need to have the values of thse Groups filled.

Example:

 - AttributeGroup Color
   -AttributeValue Red
   -AttributeValue Blue

 - product (defines allowed groups for color)
   - variant-red (defines the AttributeValue Red in attributes)
   - variant-blue (defines the AttributeValue Blue in attributes)

## Usage
CoreShop Variant Bundle does NOT come with a Installer for certain Resources it requires. That is on purpose and you need to manually install what you need.

Per Default, it comes with 3 different Classes:

 - `CoreShopAttributeGroup`
 - `CoreShopAttributeColor`
 - `CoreShopAttributeValue`

Whereas `Color` and `Value` are two Value types and `Group` is a Group type.

You can manually import the classes from this dir ```vendor/coreshop/variant-bundle/Resources/install/pimcore/classes```

To create a "Product" Class, you need to implement the interface ```CoreShop\Component\Variant\Model\ProductVariantAwareInterface```. The Class requires you to have these fields:

 - attributes - ManyToManyObjectRelation for `CoreShopAttributeColor`/`CoreShopAttributeValue`
 - allowedAttributeGroups - ManyToManyObjectRelation for `CoreShopAttributeGroup`
 - mainVariant - ManyToOneRelation for `Product` (eg. your Class where you implemented the interface)

