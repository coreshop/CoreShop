# Extend CoreShop Resources

All models in Coreshop are placed in the ```Coreshop\Component\*ComponentName*\Model``` namespaces alongside with their interfaces.

> Many models in CoreShop are extended in the Core component. If the model you are willing to override exists in the Core you should be extending the Core one, not the base model from the component.

## How to Customize a Model

First things first: If you want to extend coreshop models your Bundle must extend the `AbstractResourceBundle`. Next you have set your supported drivers. Just add the following lines of code to your bundle class:

```php
public function getSupportedDrivers()
{
    return [
        CoreShopResourceBundle::DRIVER_DOCTRINE_ORM
    ];
}
 ```
 After that have to tell the bundle where your models are. For that, add the override the following method in your bundle class and return the model namespace. Here is an example for the `AppBundle`
 
```php 
 protected function getModelNamespace()
{
    return "AppBundle\Model";
} 
```
Here a quick overview for you which dictories are important for you, when customizing CoreShop models.

| Folder | Description |
|--------|-------------|
| `AcmeBundle/Model` or `AcmeBundle/Entity` | Where your models are living |
| `AcmeBundle/config/doctrine/model` | Put your doctrine `.yml` config files in here |
| `AcmeBundle/config/serializer` | The serializer configs for the models|


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

namespace AppBundle\Entity;

use CoreShop\Component\Core\Model\Currency as BaseCurrency;

class Currency extends BaseCurrency
{
    /**
     * @var bool
     */
    private $flag;

    /**
     * @return bool
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * @param bool $flag
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;
    }
}
```

**2**. Next define your entity’s mapping.

The file should be placed in ```AppBundle/Resources/config/doctrine/Currency.orm.yml```

```yaml
AppBundle\Entity\Currency:
    type: mappedSuperclass
    table: coreshop_currency
    fields:
        flag:
            type: boolean
            nullable: true
```

**3**. Finally you’ll need to override the model’s class in the ```app/config/config.yml```.

Under the core_shop_* where * is the name of the bundle of the model you are customizing, in our case it will be the CoreShopCurrencyBundle -> core_shop_currency.


```yaml
core_shop_currency:
    resources:
        currency:
            classes:
                model: AppBundle\Entity\Currency
```

**4**. Update the database. There are two ways to do it.

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
