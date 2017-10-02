
#### CoreShop Extend Shipping Rule Action

1. We need to create 2 new files:
    - FormType for processing the Input Data
    - And a CarrierPriceActionProcessorInterface, which calculates the shipping price

```
//AppBundle/Shipping/Form/Type/Action/MyActionConfigurationType.php

namespace AppBundle\Shipping\Form\Type\Action;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class MyActionConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('myActionData', IntegerType::class, [
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
//AppBundle/Shipping/Rule/Action/MyActionConditionChecker.php

namespace AppBundleShippingBundle\Rule\Action;

class MyActionConditionChecker implements CarrierPriceActionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPrice(CarrierInterface $carrier, AddressInterface $address, array $configuration, $withTax = true)
    {
        //You can either return a float here, or false, false means no price and determines the price somewhere else
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getModification(CarrierInterface $carrier, AddressInterface $address, $price, array $configuration)
    {
        //You can return any float value here, positiv or negative

        return $configuration['myActionData'];
    }
}
```

2. Register MyRuleConditionChecker as service with tag ```coreshop.shipping_rule.condition```, type and form

```
app.coreshop.shipping_rule.condition.my_rule:
    class: AppBundle\Shipping\Rule\Action\MyActionConditionChecker
    tags:
      - { name: coreshop.shipping_rule.action, type: my_action, form-type: AppBundle\Shipping\Form\Type\ACtion\MyActionConditionChecker }
```