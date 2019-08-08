# Customizing Forms

The forms in CoreShop are placed in the ``CoreShop\Bundle\*BundleName*\Form\Type`` namespaces and the extensions
will be placed in `AppBundle\Form\Extension`.

## Why would you customize a Form?

There are plenty of reasons to modify forms that have already been defined in CoreShop.
Your business needs may sometimes slightly differ from our internal assumptions.

You can:

* add completely **new fields**,
* **modify** existing fields, make them required, change their HTML class, change labels etc.,
* **remove** fields that are not used.

## How to customize a CoreShop Resource Form?

If you want to modify the form for the ``Store`` in your system there are a few steps that you should take.
Assuming that you would like to (for example):

* Add a ```contactHours``` field,

These will be the steps that you will have to take to achieve that:

**1.** If your are planning to add new fields remember that beforehand they need to be added on the model that the form type is based on.

In case of our example if you need to have the ```contactHours``` on the model and the entity mapping for the ```Store``` resource.
To get to know how to prepare that go [there](./01_Extend_CoreShop_Resources.md)

**2.** Create a **Form Extension**.

Your form has to extend a proper base class. How can you check that?

For the ``StoreType`` run:

```bash
$ php bin/console debug:container coreshop.form.type.store
```

As a result you will get the [```CoreShop\Bundle\StoreBundle\Form\Type\StoreType```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/StoreBundle/Form/Type/StoreType.php) - this is the class that you need to be extending.

```php
<?php

namespace AppBundle\Form\Extension;

use CoreShop\Bundle\StoreBundle\Form\Type\StoreType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class StoreTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Adding new fields works just like in the parent form type.
        $builder->add('contactHours', TextType::class, [
            'required' => false
        ]);
    }

    public function getExtendedType()
    {
        return StoreType::class;
    }

    public static function getExtendedTypes()
    {
        return [StoreType::class];
    }
}
```

**3.** After creating your class, register this extension as a service in the ``AppBundle/Resources/config/services.yml``:

```yaml
services:
    app.form.extension.type.customer_profile:
        class: AppBundle\Form\Extension\StoreTypeExtension
        tags:
            - { name: form.type_extension, extended_type: CoreShop\Bundle\StoreBundle\Form\Type\StoreType }
```

In our case you will need to extend the ExtJs Form as well: `src/AppBundle/Resources/public/pimcore/js/store.js`.

In **ExtJs** your new store file need to like like this:

```javascript
coreshop.store.item = Class.create(coreshop.store.item, {

    getFormPanel: function ($super) {
        var panel = $super();

        panel.down("fieldset").add(
            [
                {
                    xtype: 'textfield',
                    fieldLabel: 'Contact Hours',
                    name: 'contactHours'
                }
            ]
        );

        return this.formPanel;
    }
});
```

And you need to configure it to be loaded as well:

```yaml
core_shop_store:
    pimcore_admin:
        js:
            custom_store: '/bundles/app/pimcore/js/store.js'
```

