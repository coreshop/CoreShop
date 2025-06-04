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

pimcore.registerNS("pimcore.object.tags.coreShopMoney");
pimcore.object.tags.coreShopMoney = Class.create(pimcore.object.tags.abstract, {

    type: "coreShopMoney",

    initialize: function (data, fieldConfig)
    {
        this.defaultValue = null;
        if ((typeof data === "undefined" || data === null) && fieldConfig.defaultValue) {
            data = fieldConfig.defaultValue;
            this.defaultValue = data;
        }

        this.data = data;
        this.fieldConfig = fieldConfig;
    },

    getGridColumnEditor: function (field)
    {
        var editorConfig = {};

        if (field.config) {
            if (field.config.width) {
                if (intval(field.config.width) > 10) {
                    editorConfig.width = field.config.width;
                }
            }
        }

        if (field.layout.noteditable) {
            return null;
        }

        if (field.type === "numeric") {
            // we have to use Number since the spinner trigger don't work in grid -> seems to be a bug of Ext
            return new Ext.form.field.Number(editorConfig);
        }
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

            return Ext.util.Format.htmlEncode(coreshop.util.format.number(value));

        }.bind(this, field.key);

        return {text: t(field.label), sortable:true, dataIndex:field.key, renderer:renderer,
            editor:this.getGridColumnEditor(field)};
    },

    getGridColumnFilter: function (field)
    {
        return {
            type: 'numeric',
            dataIndex: field.key
        };
    },

    getLayoutEdit: function ()
    {
        var input = {
            fieldLabel: this.fieldConfig.title,
            name: this.fieldConfig.name,
            componentCls: "object_field"
        };

        if (!isNaN(this.data)) {
            input.value = this.data;
        }

        if (this.fieldConfig.width) {
            input.width = this.fieldConfig.width;
        } else {
            input.width = 350;
        }

        if (this.fieldConfig.labelWidth) {
            input.labelWidth = this.fieldConfig.labelWidth;
        }
        input.width += input.labelWidth;

        if (is_numeric(this.fieldConfig["minValue"])) {
            input.minValue = this.fieldConfig.minValue;
        }

        if (is_numeric(this.fieldConfig["maxValue"])) {
            input.maxValue = this.fieldConfig.maxValue;
        }

        input.decimalPrecision = pimcore.settings.coreshop_money.decimal_precision;

        this.component = new Ext.form.field.Number(input);
        return this.component;
    },


    getLayoutShow: function ()
    {
        var input = {
            fieldLabel: this.fieldConfig.title,
            name: this.fieldConfig.name,
            componentCls: "object_field"
        };

        if (!isNaN(this.data)) {
            input.value = this.data;
        }

        if (this.fieldConfig.width) {
            input.width = this.fieldConfig.width;
        }

        if (this.fieldConfig.labelWidth) {
            input.labelWidth = this.fieldConfig.labelWidth;
        }

        input.width += input.labelWidth;

        this.component = new Ext.form.field.Number(input);
        this.component.disable();

        return this.component;
    },

    getValue: function ()
    {
        if (this.isRendered()) {
            var value = this.component.getValue();

            if (value === null) {
                return value;
            }

            return value.toString();
        } else if (this.defaultValue) {
            return this.defaultValue;
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

        if (this.defaultValue) {
            return true;
        }

        if (this.component && typeof this.component.isDirty === "function") {
            if (this.component.rendered) {
                dirty = this.component.isDirty();

                // once a field is dirty it should be always dirty (not an ExtJS behavior)
                if (this.component["__pimcore_dirty"]) {
                    dirty = true;
                }
                if (dirty) {
                    this.component["__pimcore_dirty"] = true;
                }

                return dirty;
            }
        }

        return false;
    }
});
