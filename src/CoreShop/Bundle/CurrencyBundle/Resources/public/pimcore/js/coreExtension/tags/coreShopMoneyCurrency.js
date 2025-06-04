/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS("pimcore.object.tags.coreShopMoneyCurrency");
pimcore.object.tags.coreShopMoneyCurrency = Class.create(pimcore.object.tags.abstract, {

    type: "coreShopMoneyCurrency",

    initialize: function (data, fieldConfig)
    {
        this.data = data;
        this.fieldConfig = fieldConfig;
    },

    getLayoutEdit: function ()
    {
        var container = {
            xtype: 'fieldcontainer',
            fieldLabel: this.fieldConfig.title,
            name: this.fieldConfig.name,
            componentCls: "object_field",
            layout: 'hbox',
        };
        var valueField = {
            xtype: 'numberfield',
            name: 'value',
            flex: 1,
            decimalPrecision: pimcore.globalmanager.get('coreshop.currency.decimal_precision')
        };
        var currencyField = {
            xtype: 'coreshop.currency',
            value: this.data.currency,
            name: 'currency',
            flex: 1,
            fieldLabel: null
        };

        if (!isNaN(this.data.value)) {
            valueField.value = this.data.value;
        }
        if (!isNaN(this.data.currency)) {
            currencyField.value = this.data.currency;
        }

        if (this.fieldConfig.width) {
            container.width = this.fieldConfig.width;
        } else {
            container.width = 350;
        }

        if (this.fieldConfig.labelWidth) {
            container.labelWidth = this.fieldConfig.labelWidth;
        }
        //container.width += container.labelWidth + valueField.width + currencyField.width;

        if (is_numeric(this.fieldConfig["minValue"])) {
            valueField.minValue = this.fieldConfig.minValue;
        }

        if (is_numeric(this.fieldConfig["maxValue"])) {
            valueField.maxValue = this.fieldConfig.maxValue;
        }

        container.items = [
            valueField,
            currencyField
        ];

        this.component = new Ext.create(container);

        return this.component;
    },

    getGridColumnConfig:function (field) {
        var renderer = function (key, value, metaData, record) {
            this.applyPermissionStyle(key, value, metaData, record);

            try {
                if (record.data.inheritedFields && record.data.inheritedFields[key] && record.data.inheritedFields[key].inherited == true) {
                    metaData.tdCls += " grid_value_inherited";
                }
            } catch (e) {
                console.log(e);
            }

            if (!value) {
                return '';
            }

            return Ext.util.Format.htmlEncode(coreshop.util.format.currency(value.currency.isoCode, value.value));

        }.bind(this, field.key);

        return {text: t(field.label), sortable:true, dataIndex:field.key, renderer:renderer,
            editor:this.getGridColumnEditor(field)};
    },

    getLayoutShow: function ()
    {
        this.getLayoutEdit();

        this.component.disable();

        return this.component;
    },

    getValue: function ()
    {
        if (this.isRendered()) {
            var value = this.component.down('[name="value"]').getValue();
            var currency = this.component.down('[name="currency"]').getValue();

            return {
                value: value,
                currency: currency
            };
        }

        return this.data;
    },

    getName: function ()
    {
        return this.fieldConfig.name;
    },

    isInvalidMandatory: function ()
    {
        if (!this.isRendered() && (!empty(this.getInitialData() || this.getInitialData() === 0) )) {
            return false;
        } else if (!this.isRendered()) {
            return true;
        }

        return this.getValue();
    },

    isDirty: function ()
    {
        var dirty = false;

        var components = [
            this.component.down('[name="value"]'),
            this.component.down('[name="currency"]')
        ];

        for (var i = 0; i < components.length; i++) {
            var component = components[i];

            if (component && typeof component.isDirty === "function") {
                if (component.rendered) {
                    dirty = component.isDirty();

                    // once a field is dirty it should be always dirty (not an ExtJS behavior)
                    if (component["__pimcore_dirty"]) {
                        dirty = true;
                    }
                    if (dirty) {
                        component["__pimcore_dirty"] = true;
                    }

                    if (dirty) {
                        return dirty;
                    }
                }
            }
        }

        return false;
    }
});
