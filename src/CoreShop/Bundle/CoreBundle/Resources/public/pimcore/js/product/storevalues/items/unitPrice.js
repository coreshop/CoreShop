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

pimcore.registerNS('coreshop.product.storeValues.items.unitPrice');
coreshop.product.storeValues.items.unitPrice = Class.create(coreshop.product.storeValues.items.abstract, {

    storeUnitPriceFieldSet: null,
    form: null,

    getForm: function () {

        var form = new Ext.form.FormPanel({
            border: false
        });

        this.form = form;

        return form;

    },

    onUnitDefinitionsReadyOrChange: function (data) {

        var unitDefinitions;

        if (data !== undefined) {
            unitDefinitions = this.parseFromCurrentObject(data);
        } else {
            unitDefinitions = this.parseFromResource();
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
    },

    /**
     * Set unit price relations based on current object changed unit definitions.
     *
     * @return array
     */
    parseFromCurrentObject: function (data) {

        var unitDefinitions = [],
            unitStore = pimcore.globalmanager.get('coreshop_product_units');

        if (!data.hasOwnProperty('availableUnitDefinitions')) {
            return unitDefinitions;
        }

        if (!data.availableUnitDefinitions.hasOwnProperty('additionalUnitDefinitions')) {
            return unitDefinitions;
        }

        if (!Ext.isObject(data.availableUnitDefinitions.additionalUnitDefinitions)) {
            return unitDefinitions;
        }

        // after store tag has changed on-the-fly!
        Ext.Object.each(data.availableUnitDefinitions.additionalUnitDefinitions, function (index, unitDefinition) {
            var unitRecord = unitStore.getById(unitDefinition.unit),
                existingRecord = this.getUnitDefinitionStoreData(unitDefinition.id);

            unitDefinitions.push({
                'available': is_numeric(unitDefinition.id),
                'id': existingRecord !== null ? existingRecord.id : null,
                'value': existingRecord !== null ? existingRecord.price : 0,
                'unitDefinitionId': unitDefinition.id,
                'name': Ext.isObject(unitRecord) ? unitRecord.get('fullLabel').toString() : '--'
            });

        }.bind(this));

        return unitDefinitions;
    },

    /**
     * Set unit price relations based on object resource data.
     *
     * @return array
     */
    parseFromResource: function () {

        var unitDefinitions = [];

        if (this.builder.productUnitDefinitionsStore.getRange().length === 0) {
            return unitDefinitions;
        }

        Ext.Array.each(this.builder.productUnitDefinitionsStore.getRange(), function (record) {
            var unit = record.get('unit'),
                existingRecord = this.getUnitDefinitionStoreData(record.get('id'));

            unitDefinitions.push({
                'available': true,
                'id': existingRecord !== null ? existingRecord.id : null,
                'value': existingRecord !== null ? existingRecord.price : 0,
                'unitDefinitionId': record.get('id'),
                'name': unit['fullLabel'],
            })

        }.bind(this));

        return unitDefinitions;
    },

    getUnitDefinitionPricesField: function (unitDefinitions) {

        var fieldSet,
            labelWidth = 234,
            fieldWidth = 0;

        if (this.builder.fieldConfig.width) {
            fieldWidth = this.builder.fieldConfig.width + labelWidth;
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

            if (record.available === true) {

                fieldSet.add({
                    xtype: 'hidden',
                    name: 'productUnitDefinitionPrices.' + index + '.id',
                    value: record.id
                });

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
                    allowBlank: false,
                    minValue: 0,
                    value: record.value,
                    width: fieldWidth,
                });

            } else {
                fieldSet.add({
                    xtype: 'label',
                    style: 'font-style: italic; display: block; clear: both; margin: 5px 0 10px 0;',
                    html: (record.name) + ': ' + t('coreshop_product_unit_definition_price_not_available')
                });
            }

        }.bind(this));

        return fieldSet;
    },

    getUnitDefinitionStoreData: function (unitDefinitionId) {

        var data = null,
            productUnitDefinitionPrices = this.getDataValue('productUnitDefinitionPrices');

        if (!is_numeric(unitDefinitionId)) {
            return data;
        }

        if (!Ext.isArray(productUnitDefinitionPrices) || productUnitDefinitionPrices.length === 0) {
            return data;
        }

        Ext.Array.each(productUnitDefinitionPrices, function (definitionPrice) {
            if (definitionPrice.hasOwnProperty('unitDefinition') && parseInt(definitionPrice.unitDefinition.id) === parseInt(unitDefinitionId)) {
                data = {'id': definitionPrice.id, 'price': (parseInt(definitionPrice.price) / pimcore.globalmanager.get('coreshop.currency.decimal_factor'))};
                return false;
            }
        });

        return data;
    }
});
