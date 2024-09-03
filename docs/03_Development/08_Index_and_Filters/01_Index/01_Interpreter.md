# Index Interpreter

In CoreShop, Index Interpreters are used to prepare and transform data for the product index. You can utilize existing
interpreters or create custom ones to fit your specific needs.

## Existing CoreShop Interpreters

CoreShop comes equipped with several built-in interpreters:

- **Object**: Converts an object or an object array to relations and saves the values to the relations index.
- **ObjectId**: Transforms an object into its ID.
- **ObjectIdSum**: Calculates the sum of all IDs, which can be useful for similar products.
- **ObjectProperty**: Calls a getter method on the value.
- **Soundex**: Applies the PHP `soundex` function, useful for finding similar products.

## Creating a Custom Interpreter

To create a custom interpreter, you'll need to develop two key components:

### 1. FormType for Input Data

Create a FormType class to handle the input data for your interpreter:

```php
namespace App\CoreShop\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class MyInterpreterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            .add('myInterpreterData', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['groups' => ['coreshop']]),
                    new Type(['type' => 'numeric', 'groups' => ['coreshop']]),
                ],
            ]);
    }
}
```

### 2. Interpreter Interface Implementation

Implement the `InterpreterInterface` to define how your interpreter processes data:

```php
namespace App\CoreShop\Index\Interpreter;

use CoreShop\Component\Index\Interpreter\InterpreterInterface;

class MyInterpreter implements InterpreterInterface
{
    public function interpret($value, IndexableInterface $indexable, IndexColumnInterface $config, array $interpreterConfig = [])
    {
        // Interpretation logic goes here
        return $value;
    }
}

```

### 3. Registering the Custom Interpreter

Register your new interpreter as a service with the tag `coreshop.index.interpreter`, specifying its type and form:

```yaml
App\CoreShop\Index\Interpreter\MyInterpreter:
  tags:
    - { name: coreshop.index.interpreter, type: my_interpreter, form-type: App\CoreShop\Form\Type\MyInterpreterType }
```

With these steps, you can enhance the indexing process in CoreShop with custom logic, tailored to your specific
requirements.