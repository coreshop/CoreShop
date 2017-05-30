# Create a Custom Interpreter

1. We need to create 2 new files:
    - FormType for processing the Input Data
    - And a InterpreterInterface, which interprets the data


```
//AcmeBundle/Index/Form/Type/MyInterpreterType.php

namespace AcmeBundle\Index\Form\Type;

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

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'acme_interpter_my_interpreter';
    }
}

```

```
//AcmeBundle/Index/Interpreter/MyInterpreter.php

namespace AcmeBundle\Index\Interpreter;

class MyInterpreter implements InterpreterInterface
{
    public function interpret($value, IndexColumnInterface $config = null) {
        //Do some interpretation here

        return $value;
    }
}
```

2. Register MyInterpreter as service with tag ```coreshop.index.interpreter```, type and form

```
acme.index.interpreter.my_interpreter:
    class: AcmeBundle\Index\Interpreter\MyInterpreter
    tags:
     - { name: coreshop.index.interpreter, type: acme-my-interpreter, form-type: AcmeBundle\Index\Form\Type\MyInterpreterType}
```