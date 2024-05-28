# Theme Bundle

The CoreShop Theme Bundle offers a flexible and extensible approach to managing multiple themes within a Pimcore
installation.

## Installation Process

To install the Theme Bundle, use Composer:

```bash
$ composer require coreshop/theme-bundle:^4.0
```

### Integrating with the Kernel

Enable the bundle in the kernel by updating the `AppKernel.php` file:

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

By default, no Theme Resolver is enabled. You can enable one or more resolvers in your configuration:

```yaml
core_shop_theme:
    default_resolvers:
      pimcore_site: true
      pimcore_document_property: true
```

### Theme Resolvers

1. **Pimcore Site**: Resolves the theme based on the key of the root document of a Pimcore site. For example, if the
   site's root document is named "demo," it looks for a theme with the same name.

2. **Pimcore Document Property**: Resolves the theme based on a document property of the site, specifically the property
   named "theme."

### Implementing Custom Resolvers

You can create custom resolvers:

```php
<?php

declare(strict_types=1);

namespace App\Theme;

use CoreShop\Bundle\ThemeBundle\Service\ThemeNotResolvedException;
use CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface;

final class CustomThemeResolver implements ThemeResolverInterface
{
    public function resolveTheme(): string
    {
        if(rand() === 1) {
            throw new ThemeNotResolvedException();
        }
    
        return "custom/custom";
    }
}
```

Register your custom theme resolver:

```yaml
services:
  App\Theme\CustomThemeResolver:
    tags:
      - { name: coreshop.theme.resolver, type: custom, priority: 20 }
```
