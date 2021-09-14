# Additional Data in Order
Sometimes you need to implement additional information in your order (Besides the comment field, which is available by default in the shipping checkout step).

> **Note:** To add custom fields you need a custom checkout step.

1. Create your custom brick in the admin panel: Settings->DataObjects->ObjectBricks->+
Name it for example 'AdditionalField'. It will automatically generate the method GetAdditionalField in classes using this objectBrick.
And apply it to the classes (click green +):
 - Cart (additionalData)
 - Order (additionalData)
 - Quote (additionalData)
Add fields you need to your ObjectBrick. In our sample we use text input fields on Panel:
 - SecretField.  Following methods are automatically generated: getSecretField, setSecretField
 - SecondSecretField. Following methods are automatically generated: getSecondSecretField, setSecondSecretField

2. Add fields to your custom checkout step (`createForm()`):

```php
$form->add('af_secretField', TextType::class, [
    'required' => false,
    'label' => 'coreshop.ui.your_secret_field'
]);
$form->add('af_secondSecretField', TextType::class, [
    'required' => false,
    'label' => 'coreshop.ui.your_second_secret_field'
]);
```

3. Store data in cart (`commitStep()`)

```php
use Pimcore\Model\DataObject\Objectbrick\Data\AdditionalField;

if ($form->isSubmitted()) {
    if ($form->isValid()) {
        $formData = $form->getData();
        if(!empty($formData['af_secretField'])) {
            $brick = new AdditionalField($cart);
            $brick->setSecretField($formData['af_secretField']);
            $brick->setSecondSecretField($formData['af_secondSecretField']);
            $cart->getAdditionalData()->setAdditionalField($brick);
        }
        $cart->save();
        return true;
    }
}
```

That's it - your additional data is now available in backend.
You can access those values after reloading the page from DB:
```php
$secret = $cart->getAdditionalData()
               ->getAdditionalField()
               ->getSecretField();
```

If you want to display those fields in the overview, you need to add some JS:

```yaml
core_shop_core:
    pimcore_admin:
        js:
            filter_condition_relational_multi_select: '/bundles/app/additionalData.js'
```

This file has to extend `coreshop.order.sale.detail.abstractBlock`
and requires several methods: `initBlock`,  `updateSale`,  `getPanel`,  `getPriority` and `getPosition`.

```js
pimcore.registerNS('coreshop.order.order.detail.blocks.yourBlockName');
coreshop.order.order.detail.blocks.yourBlockName = Class.create(coreshop.order.sale.detail.abstractBlock, {

    saleInfo: {},
    hasItems: true,

    initBlock: function () {
        this.saleInfo = Ext.create('Ext.panel.Panel', {
            title: t('coreshop_order_additional_data'),
            margin: '0 20 20 0',
            border: true,
            flex: 8
        });
    },

    updateSale: function () {

        var items = [],
            subItems = [];

        //console.log(this.sale.additionalData);

        var items = [], subItems = [];

        Ext.Array.each(this.sale.additionalData, function (block, i) {
            var data = block.data;
            subItems.push({
                xtype: 'label',
                style: 'font-weight:bold;display:block',
                text: 'Title for your custom field A'
            },
            {
                xtype: 'label',
                style: 'display:block',
                html: data.secretField
            });
            subItems.push({
                xtype: 'label',
                style: 'font-weight:bold;display:block',
                text: 'Title for your custom field B'
            },
            {
                xtype: 'label',
                style: 'display:block',
                html: data.secondSecretField
            })
        });

        if (subItems.length === 0) {
            this.hasItems = false;
            return;
        }

        items.push({
            xtype: 'panel',
            bodyPadding: 10,
            margin: '0 0 10px 0',
            items: subItems
        });

        // remove all before adding new ones
        // otherwise they will append during a refresh
        this.saleInfo.removeAll();

        this.saleInfo.add(items);
    },

    getPanel: function () {
        return this.hasItems ? this.saleInfo : null;
    },

    getPriority: function () {
        return 10;
    },

    getPosition: function () {
        return 'right';
    }
});
```
