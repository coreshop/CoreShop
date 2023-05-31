# CoreShop Filter - Create Custom Filter

**1**. We need to create 2 new files:
    - FormType for processing the Input Data
    - And a FilterConditionProcessorInterface, which checks if a cart fulfills the condition.

```php
namespace AppBundle\Filter\Form\Type\Condition;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class MyFilterCondition extends AbstractType
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

```php
namespace AppBundle\Filter;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;

class MyFilterCondition extends FilterConditionProcessorInterface
{
    public function prepareValuesForRendering(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter)
    {
        //Prepare values for rendering HTML
    }

    public function addCondition(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter, ParameterBag $parameterBag, $isPrecondition = false)
    {
        //Add Condition to Listing

        return $currentFilter;
    }

}
```

**2**. Register MyFilterCondition as service with tag ```coreshop.filter.condition_type```, type and form

```yaml
app.coreshop.shipping_rule.condition.my_rule:
    class: AppBundle\Shipping\Rule\Condition\MyRuleConditionChecker
    tags:
      - { name: coreshop.shipping_rule.condition, type: my_rule, form-type: AppBundle\Shipping\Form\Type\Condition\MyRuleConfigurationType }

app.filter.condition_type.my_filter_condition:
    class: AppBundle\Filter\MyFilterCondition
    tags:
      - { name: coreshop.filter.condition_type, type: app-my-filter, form-type: AppBundle\Filter\Form\Type\Condition\MyFilterCondition}
```
