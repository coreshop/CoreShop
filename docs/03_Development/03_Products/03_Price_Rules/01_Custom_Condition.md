---
title: Custom Condition
---

# Creating Custom Product Price Rule Conditions

CoreShop offers a variety of default conditions for product price rules. However, there may be instances where you need to implement custom conditions. This guide will walk you through the process of creating a custom condition for product price rules in CoreShop.

## Step 1: Create the Condition Class

Start by creating your custom condition class. This class should implement the `ConditionCheckerInterface` and define the `isValid` method, which determines if the condition is met.

```php
<?php
// src/CoreShop/Product/PriceRules/Condition/CustomCondition.php
declare(strict_types=1);

namespace App\CoreShop\Product\PriceRules\Condition;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

class CustomCondition implements ConditionCheckerInterface
{
    public function isValid(ResourceInterface $subject, RuleInterface $rule, array $configuration, $params = []): bool
    {
        if (!$subject instanceof ProductInterface) {
            return false;
        }

        // Implement your custom logic here
        // Return true if valid, false otherwise
        return true;
    }
}
```

Register the condition in your services configuration:

```yaml
# config/services.yaml
services:
  App\CoreShop\Product\PriceRules\Condition\CustomCondition:
    tags:
      - { name: coreshop.product_price_rule.condition, type: custom }
```


## Step 2: Adding Configuration Options (Optional)

If your condition requires configuration, create a form type for it:

```php
<?php
// src/CoreShop/Form/Type/PriceRules/Condition/CustomCondition.php

declare(strict_types=1);

namespace App\CoreShop\Form\Type\PriceRules\Condition\CustomCondition;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\InputType;
use Symfony\Component\Form\FormBuilderInterface;

class CustomConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('textField', InputType::class);
    }
}
```

Create a corresponding Pimcore Backend UI JavaScript file:

```javascript
pimcore.registerNS('coreshop.product.pricerules.conditions.custom');
coreshop.product.pricerule.conditions.custom = Class.create(coreshop.rules.conditions.abstract, {
    type: 'custom',

    getForm: function () {
        var textFieldValue = this.data && this.data.textField ? this.data.textField : null;

        this.textField = new Ext.create({
            xtype: 'textfield',
            name: 'text-field',
            value: textFieldValue
        });

        this.form = new Ext.form.Panel({
            items: [this.textField]
        });

        return this.form;
    }
});
```

Extend the service definition to include your form type:

```yaml
# config/services.yaml
services:
    App\CoreShop\Product\PriceRules\Condition\CustomCondition:
        tags:
            - { name: coreshop.product_price_rule.condition, type: custom, form-type: App\CoreShop\Form\Type\PriceRules\Condition\CustomCondition\CustomConditionType }
```

## Resolving Autowiring Issues with Custom Conditions

When autowiring is enabled in your CoreShop project, you may encounter a scenario where your custom condition appears twice in the system: once with configuration options and once without. This duplication can be resolved by modifying your service definition to explicitly disable autoconfiguration for your custom condition. Here’s how you can achieve this:

In your `services.yaml`, update the service definition for your custom condition as follows:

```yaml
# config/services.yaml
services:
    App\CoreShop\Product\PriceRules\Condition\CustomCondition:
        autoconfigure: false
        tags:
            - { name: coreshop.product_price_rule.condition, type: custom, form-type: App\CoreShop\Form\Type\PriceRules\Condition\CustomCondition\CustomConditionType }
```

This modification tells Symfony not to autoconfigure the custom condition service, thus preventing the duplication issue. With this change, your custom condition will appear only once in the CoreShop system with the intended configuration.
