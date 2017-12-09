# Additional Data in Order
Sometimes you need to implement additional information in yor order (Besides the comment field, which is available by default in the shipping checkout step).

> **Note:** To add custom fields you need a custom checkout step.

1. Create your custom brick and apply it to the classes:
 - Cart (additionalData)
 - Order (additionalData)
 - Quote (additionalData)

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
        return TRUE;
    }
}
```

That's it - your additional data is now available in backend.
If you want to display those fields in the overview, you need to add some JS:

```yaml
core_shop_core:
    pimcore_admin:
        js:
            filter_condition_relational_multi_select: '/bundles/app/additionalData.js'
```

This file needs to implement a method called `getItems` and must return a array:

```js
pimcore.registerNS('coreshop.order.sale.detail.additionalData');
coreshop.order.sale.detail.additionalData = Class.create({

    order: null,
    additionalData: null,

    initialize: function (order, additionalData) {
        this.order = order;
        this.additionalData = additionalData;
    },

    getItems: function () {

        //console.log(this.additionalData);

        var items = [], subItems = [];

        Ext.Array.each(this.additionalData, function (block, i) {
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

        items.push({
            xtype: 'panel',
            bodyPadding: 10,
            margin: '0 0 10px 0',
            items: subItems
        });

        return items;
    }
});
```