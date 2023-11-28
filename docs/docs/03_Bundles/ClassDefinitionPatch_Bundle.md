# Class Definition Patch Bundle

The CoreShop Class Definition Patch Bundle provides a convenient way to modify class definitions from other bundles.
This is particularly useful for adding new properties to existing classes through a configuration setup.

## Installation

```bash
$ composer require coreshop/class-definitions-patch-bundle:^4.0
```

### Adding required bundles to kernel

You need to enable the bundle inside the kernel.

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\ClassDefinitionPatchBundle\CoreShopClassDefinitionPatchBundle(),
    ]);
}
```

## Usage

### Configuration for Patching

To patch a Pimcore class definition, add a configuration like the following:

```yaml
core_shop_class_definition_patch:
    patches:
        CoreShopCompany:
            interface: 'blub'
            fields:
                name2:
                    before: 'addresses'
                    definition:
                        fieldtype: input
                        title: coreshop.company.name2
```

### Patching CoreShop Classes

For patching CoreShop classes, leverage the parameters provided by CoreShop:

```yaml
core_shop_class_definition_patch:
    patches:
        %coreshop.model.order.pimcore_class_name%:
            fields:
                total2:
                    before: 'total'
                    definition:
                        fieldtype: coreShopMoney
                        title: Total 2
```

### Preview Changes

Before applying the changes, preview them using:

```bash
bin/console coreshop:patch:classes --dump
```

### Applying the Patch

Once you're satisfied with the preview, apply the patch:

```bash
bin/console coreshop:patch:classes --force
```

> **Note** that the patch modifies your local definitions, which should be part of your version control (e.g., git
> repository). After applying the patch, commit these changes. There's no need to run the patch command during
> deployment.