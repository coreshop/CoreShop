# CoreShop Class Definition Patch Bundle

CoreShop Class Definition Patch Bundle allows you to patch class definitions from other bundles. This is useful if you want to add new properties to existing classes with a configuration.

## Installation
```bash
$ composer require coreshop/class-definitions-patch-bundle:^3.0
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

### Usage

Add a configuration to patch a certain pimcore class definition:

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

You can then run the command to preview it:

```bash
bin/console coreshop:patch:classes --dump
```



If you are happy with what you see, you can apply the patch. Since the patch is applied to your local definitions, and they should be part of your git repository, you should commit the changes and don't have to run the patch command on the deployment.

```bash
bin/console coreshop:patch:classes --force
```