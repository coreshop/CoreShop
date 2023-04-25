# CoreShop Theme Bundle

CoreShop Theme Bundle provides you with a flexible and extensible way of having multiple themes in a Pimcore installation.

## Installation
```bash
$ composer require coreshop/theme-bundle:^3.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel.

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\ThemeBundle\CoreShopThemeBundle(),
    ]);
}
```

## Configuration
Per Default, no Theme Resolver is enabled, you can enable on or multiples like:

```yaml
core_shop_theme:
    default_resolvers:
      pimcore_site: true
      pimcore_document_property: true
```

### Pimcore Site
Resolves the Theme based on the Key of the Root Document of a Pimcore Site. So if the Site's Root Document is called "demo" it tries to find a theme called the same.

### Pimcore Document Property
Resolves the Theme based on a Document Property of the Site. The Property is called "theme".

### Custom Resolvers
You can also add custom resolvers like:

```php
<?php

declare(strict_types=1);

namespace App\Theme;

use CoreShop\Bundle\ThemeBundle\Service\ThemeNotResolvedException;
use CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;

final class CustomThemeResolver implements ThemeResolverInterface
{
    public function resolveTheme(): string
    {
        if(rand() === 1) {
            // if you cannot resolve the theme, throw an exception
            throw new ThemeNotResolvedException();
        }
    
        return "custom/custom";
    }
}
```

You also need to Register the Theme Resolver:


```yaml
services:
  App\Theme\CustomThemeResolver:
    tags:
      - { name: coreshop.theme.resolver, type: custom, priority: 20 }


```