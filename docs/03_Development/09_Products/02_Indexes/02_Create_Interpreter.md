# Create a Custom Interpreter

Create a file in your Plugin or in your Website.

```
YourPlugin/lib/CoreShop/IndexService/Interpreter/YourInterpreter.php
```

```php
namespace CoreShop\IndexService\Interpreter;

class YourInterpreter extends AbstractInterpreter
{
    public function interpret($value, $config = null)
    {
        //Do something with $value here
        return $value;
    }
}

```

You now need to register your new Interpreter to CoreShop:

```php
CoreShop\IndexService\Interpreter\AbstractInterpreter::addInterpreter('YourInterpreter');
```

## Add Paramters for your Interpreter

You can also add some parameters to your Interpreter.

Create a file in your Plugin or Website. I would prefer creating a Plugin to do this, since I am not sure how to add JS files for Pimcore Admin.

```
YourPlugin/static/js/coreshop/indexes/interpreter/yourInterpreter.js
```

```js
pimcore.registerNS('pimcore.plugin.coreshop.indexes.interpreters.yourinterpreter');

pimcore.plugin.coreshop.indexes.interpreters.yourinterpreter = Class.create(pimcore.plugin.coreshop.indexes.interpreters.abstract, {

    getLayout : function (record) {
        return [
            {
                xtype : 'textfield',
                fieldLabel : 'parameter',
                name : 'parameter',
                length : 255,
                value : record.data.interpreterConfig ? record.data.interpreterConfig.parameter : null
            }
        ];
    }

});

```

You can now use the parameter in your Interpreter


```php
namespace CoreShop\IndexService\Interpreter;

class YourInterpreter extends AbstractInterpreter
{
    public function interpret($value, $config = null)
    {
        $config = isset($config) ? $config->getInterpreterConfig() : [];

        if($value instanceof AbstractObject) {
            if (array_key_exists("parameter", $config)) {
                $parameter = $config['parameter'];

                //Do something with your $parameter here
            }
        }

        //Do something with $value here
        return $value;
    }
}

```