# Customizing Forms

The forms in CoreShop are placed in the `CoreShop\Bundle\*BundleName*\Form\Type` namespaces and the extensions will be
placed in `App\CoreShop\Form\Extension`.

## Why would you customize a Form?

There are plenty of reasons to modify forms that have already been defined in CoreShop. Your business needs may
sometimes slightly differ from our internal assumptions.

You can:

* add completely **new fields**,
* **modify** existing fields, make them required, change their HTML class, change labels etc.,
* **remove** fields that are not used.

## How to customize a CoreShop Resource Form?

If you want to modify the form for the `Store` in your system there are a few steps that you should take. Assuming that
you would like to (for example):

* Add a `contactHours` field,

These will be the steps that you will have to take to achieve that:

**1.** If your are planning to add new fields remember that beforehand they need to be added on the model that the form
type is based on.

In case of our example if you need to have the `contactHours` on the model and the entity mapping for the `Store`
resource. To get to know how to prepare that go [there](./01_Extend_CoreShop_Resources.md).

**2.** Create a **Form Extension**.

Your form has to extend a proper base class. How can you check that?

For the `StoreType` run:

```bash
$ php bin/console debug:container coreshop.form.type.store
```

As a result you will get
the [```CoreShop\Bundle\StoreBundle\Form\Type\StoreType```](https://github.com/coreshop/CoreShop/blob/2026.x/src/CoreShop/Bundle/StoreBundle/Form/Type/StoreType.php) -
this is the class that you need to be extending.

```php
<?php

namespace App\CoreShop\Form\Extension;

use CoreShop\Bundle\StoreBundle\Form\Type\StoreType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class StoreTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Adding new fields works just like in the parent form type.
        $builder->add('contactHours', TextType::class, [
            'required' => false
        ]);
    }

    public static function getExtendedTypes(): array
    {
        return [StoreType::class];
    }
}
```

**3.** After creating your class, register this extension as a service in the `config/services.yaml`:

```yaml
services:
  App\CoreShop\Form\Extension\StoreTypeExtension:
    tags:
      - { name: form.type_extension, extended_type: CoreShop\Bundle\StoreBundle\Form\Type\StoreType }
```

Pimcore Studio picks the new field up automatically: the StudioFormBundle introspects the (extended) Symfony `StoreType`
and regenerates the React form from the JSON schema on the next request. No additional JavaScript, registration, or
asset build is needed.

If the field requires a custom widget that cannot be expressed through the default schema renderers, register a
`FormExtension` against the `coreshop.store.store.form` slot — see
[Extension System](../14_Studio/index.md#3-extension-system) for the slot naming and a
complete example.
