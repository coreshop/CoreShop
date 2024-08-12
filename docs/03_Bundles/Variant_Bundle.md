# Variant Bundle

The CoreShop Variant Bundle provides a robust framework for managing different variants of a product in your e-commerce
application.

## Installation Process

To install the Variant Bundle, use Composer:

```bash
composer require coreshop/variant-bundle:^4.0
```

### Integrating with the Kernel

Enable the bundle in the kernel by updating the `AppKernel.php` file:

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

Pimcore supports variants but does not define the specifics of what a variant is or how it differs from the parent
product or other variants. CoreShop addresses this by introducing two entities:

1. **Attribute Group** (`CoreShop\Component\Variant\Model\AttributeGroupInterface`): Defines types like size or color.
2. **Attribute Value** (`CoreShop\Component\Variant\Model\AttributeValueInterface`): Represents values like `XL`
   or `Red`.

The product is an abstract entity specifying the allowed attribute groups, with its variants needing to have these group
values filled.

### Example

- **AttributeGroup Color**: Contains `AttributeValue Red` and `AttributeValue Blue`.
- **Product**: Defines allowed groups for color.
    - **Variant-Red**: Defines the `AttributeValue Red` in attributes.
    - **Variant-Blue**: Defines the `AttributeValue Blue` in attributes.

## Usage

### Class Installation

The Variant Bundle does not come with an installer for required resources. You need to install what you need manually.
By default, it includes three classes:

- `CoreShopAttributeGroup`
- `CoreShopAttributeColor`
- `CoreShopAttributeValue`

The classes `Color` and `Value` represent value types, while `Group` is a group type. Manually import the classes
from `vendor/coreshop/variant-bundle/Resources/install/pimcore/classes`.

### Creating a "Product" Class

Implement the `CoreShop\Component\Variant\Model\ProductVariantAwareInterface` for your "Product" class. It should
include these fields:

- **attributes**: ManyToManyObjectRelation for `CoreShopAttributeColor`/`CoreShopAttributeValue`.
- **allowedAttributeGroups**: ManyToManyObjectRelation for `CoreShopAttributeGroup`.
- **mainVariant**: ManyToOneRelation for `Product` (i.e., your class implementing the interface).

The Variant Bundle significantly enhances the flexibility of product management in CoreShop, allowing for detailed and
diverse product variant configurations.

## Variant Generator
The Variant Generator is a tool that automatically generates variants for a VariantAware Class based on the attribute groups 
defined in the Data Object. The Generator is available in the Pimcore backend at the Toolbar on your VariantAware Class. 

### Installation

Variant Generator uses Symfony Messenger for async processing, you can run it with the following command:

```yaml
bin/console messenger:consume coreshop_variant --time-limit=300
```