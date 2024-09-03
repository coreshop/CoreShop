# Extend CoreShop Resources

All models in Coreshop are placed in the ```Coreshop\Component\*ComponentName*\Model``` namespaces alongside with their interfaces.

> Many models in CoreShop are extended in the Core component. If the model you are willing to override exists in the Core you should be extending the Core one, not the base model from the component.

## How to Customize a Model

### Doctrine ORM

Depending on your Doctrine ORM configuration, you need to place your Models in the right Folder. The Default configuration, places them in the `Entity` directory:

```yaml
doctrine:
  orm:
    mappings:
      App:
        type: attribute
        dir: '%kernel.project_dir%/src/App/Entity'
        is_bundle: false
        prefix: App\Entity
        alias: App
```

### Example Model

Let’s take the [```CoreShop\Component\Currency\Model\Currency```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Currency/Model/Currency.php) as an example. This one is extended in Core. How can you check that?

First of all, you need to find the current used class by doing following:

```bash
$ php bin/console debug:container --parameter=coreshop.model.currency.class
```

As a result you will get the [```CoreShop\Component\Core\Model\Currency```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Model/Currency.php) - this is the class that you need to be extending.

Assuming you want to add a field called **flag**

**1.** The first thing to do is to write your own class which will extend the base ```Currency``` class

```php
<?php

namespace App\Entity;

use CoreShop\Component\Core\Model\Currency as BaseCurrency;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="coreshop_currency")
 */
class Currency extends BaseCurrency
{
    /**
     * @ORM\Column(type="boolean")
     */
    private bool $flag = false;

    public function getFlag(): bool
    {
        return $this->flag;
    }

    public function setFlag(bool $flag): void
    {
        $this->flag = $flag;
    }
}
```

**2**. Finally you’ll need to override the model’s class in the ```config/config.yaml```.

Under the core_shop_* where * is the name of the bundle of the model you are customizing, in our case it will be the CoreShopCurrencyBundle -> core_shop_currency.


```yaml
core_shop_currency:
    resources:
        currency:
            classes:
                model: App\Entity\Currency
```

**3**. Update the database. There are two ways to do it.

via direct database schema update:

```bash
$ php bin/console doctrine:schema:update --force
```

via migrations:
Which we strongly recommend over updating the schema.

```bash
$ php bin/console doctrine:migrations:diff
$ php bin/console doctrine:migrations:migrate
```

**4**. We also need a serializer group for the new field. This is done in the ```config/jms_serializer/Currency.yaml``` file.

```yaml
App\Entity\Currency:
  exclusion_policy: ALL
  xml_root_name: store
  properties:
    flag:
      expose: true
      type: bool
      groups: [Detailed]

```