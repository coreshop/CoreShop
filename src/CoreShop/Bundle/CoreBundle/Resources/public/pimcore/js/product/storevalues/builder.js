/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('coreshop.product.storeValues.builder');
coreshop.product.storeValues.builder = Class.create({

    data: null,
    store: null,
    fieldConfig: null,

    form: null,

    uniteStore: null,

    initialize: function (fieldConfig, store, data) {

        this.fieldConfig = fieldConfig;
        this.store = store;
        this.data = data;
        this.unitStore = pimcore.globalmanager.get('coreshop_product_units');

        this.setupForm();

    },

    setupForm: function () {
        this.form = new Ext.form.Panel({
            closable: false,
            items: this.getItems()
        });
    },

    getForm: function () {
        return this.form;
    },

    getItems: function () {

        return [
            this.getFieldIdField(),
            this.getPriceField(),
            this.getDefaultUnitField(),
            this.geAdditionalUnitsField()
        ]
    },

    getDataValue: function (key) {

        var data = this.data !== null && Ext.isObject(this.data) ? this.data : null;
        if (data === null) {
            return null;
        }

        var values = data.values !== null && Ext.isObject(data.values) ? data.values : null;
        if (values === null) {
            return null;
        }

        if (values.hasOwnProperty(key)) {
            return values[key];
        }

        return null;
    },

    getFieldIdField: function () {
        return new Ext.form.NumberField({
            name: 'id',
            hidden: true,
            value: this.getDataValue('id')
        });
    },

    getPriceField: function () {

        var price = this.getDataValue('price'),
            priceField = new Ext.form.NumberField({
                fieldLabel: t('coreshop_store_values_store_price'),
                name: 'price',
                componentCls: 'object_field',
                labelWidth: 250,
                value: 0
            });

        if (price !== null) {
            priceField.setValue(price / 100);
            priceField.setFieldLabel(priceField.fieldLabel + ' (' + this.data.currencySymbol + ')');
        }

        if (this.fieldConfig.width) {
            priceField.setWidth(this.fieldConfig.width + priceField.labelWidth);
        } else {
            priceField.setWidth(350 + priceField.labelWidth);
        }

        if (is_numeric(this.fieldConfig['minValue'])) {
            priceField.setMinValue(this.fieldConfig.minValue);
        }

        if (is_numeric(this.fieldConfig['maxValue'])) {
            priceField.setMaxValue(this.fieldConfig.maxValue);
        }

        return priceField;
    },

    getDefaultUnitField: function () {

        var defaultUnit = this.getDataValue('defaultUnit'),
            defaultUnitPrecision = this.getDataValue('defaultUnitPrecision'),
            unitFieldForm = this.getUnitFormFields({
                unitName: 'defaultUnit',
                unitLabel: 'coreshop_store_values_unit_default_type',
                unitValue: defaultUnit !== null ? defaultUnit.id : this.unitStore.first(),
                precisionName: 'defaultUnitPrecision',
                precisionLabel: 'coreshop_store_values_unit_precision',
                precisionValue: defaultUnitPrecision !== null ? defaultUnitPrecision : 0,
            }, false);

        return Ext.create('Ext.form.Panel', {
            width: 750,
            items: [
                {
                    xtype: 'fieldset',
                    title: t('coreshop_store_values_default_unit_headline'),
                    layout: 'hbox',
                    itemCls: 'object_field',
                    items: unitFieldForm
                }
            ]
        });
    },

    geAdditionalUnitsField: function () {

        var additionalUnitData = this.getDataValue('additionalUnits'),
            addUnitField = function (fieldSet, data) {

                var compositeField,
                    count = fieldSet.query('button').length + 1,
                    unitFieldForm = this.getUnitFormFields({
                        unitName: 'additionalUnit.' + count + '.unit',
                        unitLabel: 'coreshop_store_values_unit_type',
                        unitValue: data !== null && Ext.isObject(data) ? data.unit.id : this.unitStore.first(),
                        precisionName: 'additionalUnit.' + count + '.precision',
                        precisionLabel: 'coreshop_store_values_unit_precision',
                        precisionValue: data !== null ? data.precision : 0,
                        conversionRateName: 'additionalUnit.' + count + '.conversionRate',
                        conversionRateLabel: 'coreshop_store_values_unit_conversion_rate',
                        conversionRateValue: data !== null ? data.conversionRate : 0,
                    }, true);

                // add id field if available
                if (data !== null && data.hasOwnProperty('id')) {
                    unitFieldForm.push({
                        xtype: 'numberfield',
                        name: 'additionalUnit.' + count + '.id',
                        value: data.id,
                        hidden: true
                    });
                }

                compositeField = new Ext.form.FieldContainer({
                    layout: 'hbox',
                    hideLabel: true,
                    itemCls: 'object_field',
                    items: unitFieldForm
                });

                compositeField.add({xtype: 'tbfill'});
                compositeField.add({
                    xtype: 'button',
                    iconCls: 'pimcore_icon_delete',
                    handler: function (compositeField, el) {
                        fieldSet.remove(compositeField);
                        fieldSet.updateLayout();
                    }.bind(this, compositeField)
                });

                fieldSet.add(compositeField);
                fieldSet.updateLayout();

            }.bind(this);

        var fieldSet = new Ext.form.FieldSet({
            title: t('coreshop_store_values_additional_units_headline'),
            collapsible: false,
            autoHeight: true,
            width: 750,
            style: 'margin-top: 20px;',
            items: [{
                xtype: 'toolbar',
                style: 'margin-bottom: 10px; padding: 5px;',
                items: ['->', {
                    xtype: 'button',
                    iconCls: 'pimcore_icon_add',
                    handler: function (b) {
                        var fieldSet = b.up('fieldset');
                        addUnitField(fieldSet, null);
                    }.bind(this)
                }]
            }]
        });

        if (additionalUnitData !== null && Ext.isArray(additionalUnitData)) {
            Ext.Array.each(additionalUnitData, function (unit) {
                addUnitField(fieldSet, unit);
            });
        }

        return fieldSet;
    },

    getUnitFormFields: function (data, extended) {

        var fields = [
            {
                xtype: 'combo',
                fieldLabel: t(data.unitLabel),
                name: data.unitName,
                labelWidth: 80,
                store: this.unitStore,
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                queryMode: 'local',
                displayField: 'name',
                valueField: 'id',
                value: data.unitValue,
                maxWidth: 190,
            },
            {
                xtype: 'numberfield',
                fieldLabel: t(data.precisionLabel),
                name: data.precisionName,
                labelWidth: 80,
                minValue: 0,
                value: data.precisionValue,
                maxWidth: 160,
            }
        ];

        if (extended === true) {
            fields.push({
                xtype: 'numberfield',
                fieldLabel: t(data.conversionRateLabel),
                name: data.conversionRateName,
                labelWidth: 120,
                minValue: 0,
                value: data.conversionRateValue,
                decimalPrecision: 2,
                maxWidth: 220,
            });

            fields.push({
                xtype: 'label',
                text: 'item',
                style: 'margin: 7px 0 0 -4px;'
            });
        }

        return fields;

    },

    postSaveObject: function () {
        this.form.getForm().getFields().each(function (item) {
            item.resetOriginalValue();
        });
    },

    isDirty: function () {
        if (this.form.getForm().isDirty()) {
            return true;
        }

        return false;
    },

    getValues: function () {
        return this.form.getForm().getFieldValues();
    }
});