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
    objectId: null,
    store: null,
    fieldConfig: null,
    form: null,
    dirty: false,

    // unit section
    productUnitDefinitionsStore: null,
    storeUnitPriceFieldSet: null,

    initialize: function (fieldConfig, store, data, productUnitDefinitionsStore, objectId) {

        this.fieldConfig = fieldConfig;
        this.store = store;
        this.data = data;
        this.productUnitDefinitionsStore = productUnitDefinitionsStore;
        this.objectId = objectId;
        this.dirty = false;

        this.setupForm();

    },

    setupForm: function () {
        this.form = new Ext.form.Panel({
            closable: false
        });

        this.getItems();
    },

    getItems: function () {

        this.form.add([
            this.getPriceField()
        ]);
    },

    getForm: function () {
        return this.form;
    },

    getDataValue: function (key) {

        var data, values;

        data = this.data !== null && Ext.isObject(this.data) ? this.data : null;
        if (data === null) {
            return null;
        }

        values = data.values !== null && Ext.isObject(data.values) ? data.values : null;
        if (values === null) {
            return null;
        }

        if (values.hasOwnProperty(key)) {
            return values[key];
        }

        return null;
    },

    getPriceField: function () {

        var price = this.getDataValue('price'),
            priceField = new Ext.form.NumberField({
                fieldLabel: t('coreshop_store_values_store_price'),
                name: 'price',
                componentCls: 'object_field',
                labelWidth: 250,
                minValue: 0,
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

    onUnitDefinitionsReadyOrChange: function (data) {

        var unitDefinitions = [];

        this.form.setLoading(true);

        if (data === undefined) {
            // initial from store!
            if (this.productUnitDefinitionsStore.getRange().length > 0) {
                Ext.Array.each(this.productUnitDefinitionsStore.getRange(), function (record) {
                    var unit = record.get('unit');
                    unitDefinitions.push({
                        'unitDefinitionId': record.get('id'),
                        'name': unit['name'],
                    })
                }.bind(this));
            }
        } else if (Ext.isObject(data) && Ext.isArray(data.availableUnits)) {
            // after store tag has changed on-the-fly!
            Ext.Array.each(data.availableUnits, function (unitBlock) {
                if (unitBlock.isDefaultUnitDefinition === false) {
                    unitDefinitions.push({
                        'unitDefinitionId': unitBlock.unit.get('id'),
                        'name': unitBlock.unit.get('name'),
                    })
                }
            });
        }

        if (this.storeUnitPriceFieldSet !== null) {
            this.form.remove(this.storeUnitPriceFieldSet);
        }

        // do not show extra unit fields if no product unit definitions are available.
        if (unitDefinitions.length === 0) {
            return;
        }

        this.storeUnitPriceFieldSet = this.getUnitDefinitionPricesField(unitDefinitions);

        this.form.add(this.storeUnitPriceFieldSet);
        this.form.setLoading(false);
    },

    getUnitDefinitionPricesField: function (unitDefinitions) {

        var fieldSet,
            labelWidth = 234,
            fieldWidth = 0,
            productUnitDefinitionPrices = this.getDataValue('productUnitDefinitionPrices');

        if (this.fieldConfig.width) {
            fieldWidth = this.fieldConfig.width + labelWidth;
        } else {
            fieldWidth = 350 + labelWidth;
        }

        fieldSet = new Ext.form.FieldSet({
            title: t('coreshop_store_values_store_unit_prices'),
            collapsible: false,
            autoHeight: true,
            style: 'margin-top: 20px;',
            itemId: 'unit-store-prices-fieldset',
            items: []
        });

        Ext.Array.each(unitDefinitions, function (record, index) {
            fieldSet.add({
                xtype: 'hidden',
                name: 'productUnitDefinitionPrices.' + index + '.unitDefinition',
                value: record.unitDefinitionId
            });
            fieldSet.add({
                xtype: 'numberfield',
                fieldLabel: record.name,
                name: 'productUnitDefinitionPrices.' + index + '.price',
                labelWidth: labelWidth,
                minValue: 0,
                value: this.getUnitDefinitionStorePrice(productUnitDefinitionPrices, record.unitDefinitionId),
                width: fieldWidth,
            })
        }.bind(this));

        return fieldSet;
    },

    getUnitDefinitionStorePrice(productUnitDefinitionPrices, definitionId) {

        var price = 0;
        if (productUnitDefinitionPrices === null || !Ext.isArray(productUnitDefinitionPrices)) {
            return price;
        }

        Ext.Array.each(productUnitDefinitionPrices, function (definitionPrice) {
            if (definitionPrice.hasOwnProperty('unitDefinition') && parseInt(definitionPrice.unitDefinition.id) === parseInt(definitionId)) {
                price = parseInt(definitionPrice.price) / 100;
                return false;
            }
        });

        return price;
    },

    postSaveObject: function (object, refreshedData) {

        if (Ext.isObject(refreshedData) && Ext.isObject(refreshedData.values)) {
            this.data.values = refreshedData.values;
        }

        this.dirty = false;

        this.form.getForm().getFields().each(function (item) {
            item.resetOriginalValue();
        });
    },

    isDirty: function () {

        if (this.dirty === true) {
            return true;
        }

        if (this.form.getForm().isDirty()) {
            return true;
        }

        return false;
    },

    getValues: function () {
        var formValues = this.form.getForm().getFieldValues();
        if (this.getDataValue('id') !== null) {
            formValues['id'] = this.getDataValue('id');
        }

        return formValues;
    }
});
