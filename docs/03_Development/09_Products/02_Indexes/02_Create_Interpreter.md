# Create a Custom Interpreter

Create a file in your Plugin or in your Website.

```
Website/lib/Website/IndexService/Interpreter/YourInterpreter.php

or

YourPlugin/lib/YourPlugin/IndexService/Interpreter/YourInterpreter.php
```

```php
namespace Website\IndexService\Interpreter;

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
\Pimcore::getEventManager()->attach('coreshop.indexService.interpreter.init', function(\Zend_EventManager_Event $e) {
    $target = $e->getTarget();

    if($target instanceof \CoreShop\Bundle\LegacyBundle\Composite\Dispatcher) {
        $target->addType(Website\IndexService\Interpreter\YourInterpreter::class);
    }
});
```

## Add Parameters for your Interpreter

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
namespace CoreShop\Bundle\LegacyBundle\IndexService\Interpreter;

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