# CoreShop Index Interpreter

To prepare your index and transform data, you use one of the existing Interpreter or create one yourself.

CoreShop currently has following Interpreters:

 - **Object**: converts an object or and object array to relations. It saves the values to the relations inex
 - **ObjectId**: converts an object to its ID
 - **ObjectIdSum**: calculates the sum of all IDs. (Could be used for similar products)
 - **ObjectProperty**: calls a getter method of the value
 - **Soundex**: calls PHP soundex function (Could be used for similar products)

## Create a Custom Interpreter

**1** We need to create 2 new files:
 - FormType for processing the Input Data
 - And a InterpreterInterface, which interprets the data

```php

namespace AppBundle\Index\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class MyInterpreterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('myInterpreterData', IntegerType::class, [
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
namespace AppBundle\CoreShop\Index\Interpreter;

use CoreShop\Component\Index\Interpreter\InterpreterInterface;

class MyInterpreter implements InterpreterInterface
{
    public function interpret($value, IndexableInterface $indexable, IndexColumnInterface $config, array $interpreterConfig = []) {
        //Do some interpretation here

        return $value;
    }
}
```

**2**:Register MyInterpreter as service with tag ```coreshop.index.interpreter```, type and form

```yaml
app.index.interpreter.my_interpreter:
    class: AppBundle\CoreShop\Index\Interpreter\MyInterpreter
    tags:
     - { name: coreshop.index.interpreter, type: my_interpreter, form-type: AppBundle\Index\Form\Type\MyInterpreterType}
```
