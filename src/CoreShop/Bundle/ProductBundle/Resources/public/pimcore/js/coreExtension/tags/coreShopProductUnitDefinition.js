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

pimcore.registerNS('pimcore.object.tags.coreShopProductUnitDefinition');
pimcore.object.tags.coreShopProductUnitDefinition = Class.create(pimcore.object.tags.abstract, {
    type: 'coreShopProductUnitDefinition',

    allowEmpty: false,
    displayField: 'unitName',

    initialize: function (data, fieldConfig) {
        this.data = data;
        this.fieldConfig = fieldConfig;
        this.fieldConfig.width = 350;
    },

    getLayoutShow: function () {

        this.component = this.getLayoutEdit();
        this.component.setReadOnly(true);

        return this.component;
    },

    getValue: function () {
        return this.data.id;
    },

    getLayoutEdit: function () {

        var options, displayValue;

        displayValue = this.data.id !== null ? this.data.unitName + ' (' + this.data.conversationRate + ')' : null;

        options = {
            name: this.fieldConfig.name,
            fieldLabel: this.fieldConfig.title,
            componentCls: 'object_field',
            fieldCls: 'pimcore_droptarget_unit_definition_input',
            width: 250,
            labelWidth: 100,
            displayField: this.displayField,
            valueField: 'id',
            queryMode: 'local',
            value: displayValue,
        };

        if (this.fieldConfig.labelWidth) {
            options.labelWidth = this.fieldConfig.labelWidth;
        }

        if (this.fieldConfig.width) {
            options.width = this.fieldConfig.width;
        }

        options.width += options.labelWidth;

        this.component = new Ext.form.TextField(options);

        return this.component;
    }

});
