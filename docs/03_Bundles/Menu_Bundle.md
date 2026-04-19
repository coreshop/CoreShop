# Menu Bundle

The Menu Bundle simplifies the creation of Pimcore menus based on permissions, enhancing the user interface and
navigation experience.

## Installation Process

To install the Menu Bundle, use Composer:

```bash
$ composer require coreshop/menu-bundle:^4.0
```

### Integrating with the Kernel

Enable the bundle in the kernel by updating the `AppKernel.php` file:

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\MenuBundle\CoreShopMenuBundle(),
    ]);
}
```

## Usage Instructions

### Creating a New Menu

1. **Define a Menu Builder Class**:
   Create a new class for your menu, such as `MyMenuBuilder`.

   ```php
   <?php
   namespace App\CoreShop\Menu;

   use CoreShop\Bundle\MenuBundle\Builder\MenuBuilderInterface;
   use Knp\Menu\FactoryInterface;
   use Knp\Menu\ItemInterface;

   class MyMenuBuilder implements MenuBuilderInterface
   {
       public function buildMenu(ItemInterface $menuItem, FactoryInterface $factory, string $type) 
       {
           $menuItem
               ->addChild('my-menu-item')
               ->setLabel('my-menu-item')
               ->setAttribute('permission', 'my_menu_item')
               ->setAttribute('iconCls', 'pimcore_icon_delete');
       }
   }
   ```

2. **Register the Menu Builder**:
   Register your menu builder class in the Symfony container.

   ```yml 
   App\CoreShop\Menu\MyMenuBuilder:
       tags:
           - { name: coreshop.menu, type: my_menu, menu: my_menu }
   ```

### Wiring the Menu into Pimcore Studio

The Menu Bundle exposes menu definitions to Pimcore Studio via the JSON API at
`/{backend}/coreshop/menus`. Studio fetches the serialized menu tree and renders it through its own React
navigation shell — no additional JavaScript is required on your side.

To react to a menu item click from a Studio plugin, subscribe to the menu event:

```typescript
// src/CoreShop/Bundle/YourBundle/Resources/assets/pimcore-studio/src/main.ts

import { IAbstractPlugin } from '@pimcore/studio-ui-bundle'

const plugin: IAbstractPlugin = {
    name: 'your-bundle',

    onInit() {
        document.addEventListener('coreshop.menu.open', (e: CustomEventInit) => {
            if (e.detail?.item?.id === 'my-menu-item') {
                // open your widget / tab / modal here
            }
        })
    },
}

export default plugin
```
