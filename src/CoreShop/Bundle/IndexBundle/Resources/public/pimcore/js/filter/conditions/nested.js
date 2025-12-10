/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.filter.conditions.nested');
coreshop.filter.conditions.nested = Class.create(coreshop.filter.conditions.abstract, {

    type: 'nested',

    operatorCombo: null,
    conditions: null,

    getDefaultItems: function () {
        this.labelField = Ext.create({
            xtype: 'textfield',
            name: 'label',
            width: 400,
            fieldLabel: t('label'),
            value: this.data.label
        });

        return [
            this.labelField
        ];
    },

    getItems: function () {
        this.conditions = new this.parent.__proto__.constructor(this.parent.parent, this.parent.conditions, 'nested');

        var layout = this.conditions.getLayout();
        layout.setTitle(null);
        layout.setIconCls(null);

        // add saved conditions
        if (this.data && this.data.configuration.conditions) {
            Ext.each(this.data.configuration.conditions, function (condition) {
                this.conditions.addCondition(condition.type, condition, false);
            }.bind(this));
        }

        return [new Ext.panel.Panel({
            items: [
                layout
            ]
        })];
    },

    getData: function () {
        var conditions = this.conditions.getData();

        return {
            configuration: {
                conditions: conditions,
            },
            label: this.labelField.getValue()
        };
    }
});
