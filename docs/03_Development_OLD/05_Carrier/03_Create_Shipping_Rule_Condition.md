
#### CoreShop Extend Shipping Rule Condition

1. We need to create 2 new files:
    - FormType for processing the Input Data
    - And a ShippingConditionCheckerInterface, which checks if a cart fulfills the condition.

```
//AppBundle/Shipping/Form/Type/Condition/MyRuleConfigurationType.php

namespace AppBundle\Shipping\Form\Type\Condition;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class MyRuleConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('myData', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['groups' => ['coreshop']]),
                    new Type(['type' => 'numeric', 'groups' => ['coreshop']]),
                ],
            ])
        ;
    }
}

```

```
//AppBundle/Shipping/Rule/Condition/MyRuleConditionChecker.php

namespace AppBundle\Shipping\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;

class MyRuleConditionChecker extends AbstractConditionChecker
{
    /**
     * {@inheritdoc}
     */
    public function isShippingRuleValid(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, array $configuration)
    {
        $minAmount = $configuration['myData'];

        //Check cart here and return true or false;

        return true || false;
    }
}
```

2. Register MyRuleConditionChecker as service with tag ```coreshop.shipping_rule.condition```, type and form

```
app.coreshop.shipping_rule.condition.my_rule:
    class: AppBundle\Shipping\Rule\Condition\MyRuleConditionChecker
    tags:
      - { name: coreshop.shipping_rule.condition, type: my_rule, form-type: AppBundle\Shipping\Form\Type\Condition\MyRuleConfigurationType }
```