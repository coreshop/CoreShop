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

### Implementing the ExtJs JavaScript Part

1. **Add a JavaScript File**:
   In your bundle's `Bundle.php` file, add the JavaScript file to the `jsPaths`
   array: `/admin/coreshop/coreshop.my_menu/menu.js`
2. **Instantiate the Menu**:
   In your `startup.js` file, instantiate the menu:

   ```javascript
    new coreshop.menu.coreshop.my_menu();
   
    pimcore.eventDispatcher.registerTarget('coreshopMenuOpen', new (Class.create({
        coreshopMenuOpen: function(type, item) {
        if (item.id === 'my-menu-item') {
            alert('My Menu Item has been clicked');
        }
    }

   ```



