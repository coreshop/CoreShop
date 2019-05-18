# CoreShop Menu Bundle

Menu Bundle makes it easy creating Pimcore Menus based on permissions.

## Installation
```bash
$ composer require coreshop/menu-bundle:^2.1
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

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

### Usage

Create a new Menu by creating a new Class, let's call it `MyMenuBuilder`

```php

namespace AppBundle\Menu;

use CoreShop\Bundle\MenuBundle\Builder\MenuBuilderInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class MyMenuBuilder implements MenuBuilderInterface
{
    public function buildMenu(ItemInterface $menuItem, FactoryInterface $factory, string $type) 
    {
        //Create a new direct sub-menu item
        $menuItem
            ->addChild('my-menu-item')
            ->setLabel('my-menu-item')
            ->setAttribute('permission', 'my_menu_item')
            ->setAttribute('iconCls', 'pimcore_icon_delete')
        ;
    }
}
```

You then need to register your class to the symfony container:

```yml 
    app.my_menu:
        class: AppBundle\Menu\MyMenuBuilder
        tags:
            - { name: coreshop.menu, type: my_menu, menu: my_menu }

```

Where the `menu` attribute defines your unique identifier for your menu. You can also register multiple Builders
for your Menu with the same `menu` attribute, this will load them one after another.

Now, lets do the ExtJs Javascript part. In your Bundle. I assume you already have a working Bundle for that. In your
`Bundle.php` file, add this file to the `jsPaths` array:

```
'/admin/coreshop/coreshop.my_menu/menu.js'
```

Where `my_menu` here again is your defined identifier. This will load a basic helper file that will build your menu.
Now, let's actually build the menu.

You should already have a `startup.js` somehwere in your Bundle. In there, you can instantiate the menu by calling:

```
new coreshop.menu.coreshop.my_menu();
```

That will build the menu automatically for you.

In order now to do something when a menu-item is clicked, you can attach to the event that is fired:

```javascript
pimcore.eventDispatcher.registerTarget('coreshopMenuOpen', new (Class.create({
    coreshopMenuOpen: function(type, item) {
        if (item.id === 'my-menu-item') {
            alert('My Menu Item has been clicked');
        }
    }
})));
```
