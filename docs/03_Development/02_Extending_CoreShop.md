# Extending CoreShop

CoreShop-Objects (like CoreShopProduct or CoreShopOrder) should not be changed directly within Pimcore.

**There are two ways of adding custom Fields:**

## Extending CoreShop Classes with a Hook

If you need some extra fields in any object, you can hook into "install.class.preCreate"

For example:

```php
\Pimcore::getEventManager()->attach("install.class.preCreate", array($this, "installClassPreCreate"));
```

```php
    public function installClassPreCreate($e)
    {
        $className = $e->getParam("className");
        $json = $e->getParam("json");

        if($className == "CoreShopProduct")
        {
            $e->stopPropagation(true);
            return $this->installClassCoreShopProduct($json);
        }
        
        return false;
    }

    public function installClassCoreShopProduct($json)
    {
        $jsonArray = \Zend_Json::decode($json);

        $jsonArray['layoutDefinitions']['childs'][0]['childs'][] = array(
            "fieldtype" => "panel",
            "labelWidth" => 100,
            "name" => "MyTab",
            "title" => "MyTab",
            "datatype" => "layout",
            "childs" => array(
                array
                (
                    'fieldtype' => 'checkbox',
                    'defaultValue' => 0,
                    'queryColumnType' => 'tinyint(1)',
                    'columnType' => 'tinyint(1)',
                    'phpdocType' => 'boolean',
                    'name' => 'needsPersonalData',
                    'title' => 'Braucht Daten',
                    'tooltip' => '',
                    'mandatory' => false,
                    'noteditable' => false,
                    'index' => false,
                    'locked' => NULL,
                    'style' => '',
                    'permissions' => NULL,
                    'datatype' => 'data',
                    'relationType' => false,
                    'invisible' => false,
                    'visibleGridView' => false,
                    'visibleSearch' => false,
                ), 
                array
                (
                    'fieldtype' => 'checkbox',
                    'defaultValue' => 0,
                    'queryColumnType' => 'tinyint(1)',
                    'columnType' => 'tinyint(1)',
                    'phpdocType' => 'boolean',
                    'name' => 'needsDoublePersonalData',
                    'title' => 'Braucht 2x Daten',
                    'tooltip' => '',
                    'mandatory' => false,
                    'noteditable' => false,
                    'index' => false,
                    'locked' => NULL,
                    'style' => '',
                    'permissions' => NULL,
                    'datatype' => 'data',
                    'relationType' => false,
                    'invisible' => false,
                    'visibleGridView' => false,
                    'visibleSearch' => false,
                )
            )
        );

        return \Zend_Json::encode($jsonArray);
    }
```

## Use your Custom Class

You can use a Custom Class for all Objects/FieldCollections/ObjectBricks CoreShop is using. Your class needs to have the same properties as eg. CoreShopProduct has, but your class will never be overwritten by the CoreShop Updater.

**How you use your Product Class for CoreShop:**

As example we take the Product Class.

To do that, you need to create your own Pimcore-Class (eg. Product) and import CoreShops-Product Class Definition into it (located within plugins/CoreShop/install) 

1. Create your "Pimcore Class" called "Product"
2. Create your custom Product class which inherits from CoreShop\Model\Product. (eg. Website\Model\Product)
3. Set the parent class of your Pimcore Class to Website\Model\Product.
4. Override the static variable $pimcoreClass to your new Class to: 'Pimcore\\Model\\Object\\Product'
5. add a classmap/di for "CoreShop\Model\Product" to "Website\Model\Product"
6. voila, CoreShop is now using your custom Product class.

This procedure works for all CoreShop Pimcore Classes, Field-Collections and Objectbricks.

**Example:**

```php
//website/models/Website/Model/Product.php
namespace Website\Model;

class Product extends \CoreShop\Model\Product {
    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = 'Pimcore\\Model\\Object\\Product';
}
```

#### For Version < 1.0.5
```php
//website/var/config/classmap.php
<?php

return [
    "CoreShop\\Model\\Product" => "Website\\Model\\Product",
];
```

#### For Version >= 1.0.5
```php
//website/var/config/di.php
<?php

return [
    'CoreShop\Model\Product' => DI\object('Website\Model\Product')
];
```


Now, CoreShop is always using your Product class, you can now extend or overwrite it.