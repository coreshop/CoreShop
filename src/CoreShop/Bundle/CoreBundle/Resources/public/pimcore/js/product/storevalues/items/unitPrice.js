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

        var unitDefinitions = [];

        if (data === undefined) {
            // initial from store!
            if (this.builder.productUnitDefinitionsStore.getRange().length > 0) {
                Ext.Array.each(this.builder.productUnitDefinitionsStore.getRange(), function (record) {
                    var unit = record.get('unit');
                    unitDefinitions.push({
                        'available': true,
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
                        'available': unitBlock.hasId === true,
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
    },

    getUnitDefinitionPricesField: function (unitDefinitions) {

        var fieldSet,
            labelWidth = 234,
            fieldWidth = 0,
            productUnitDefinitionPrices = this.getDataValue('productUnitDefinitionPrices');

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
                });

            } else {
                fieldSet.add({
                    xtype: 'label',
                    style: 'font-style: italic; display: block; clear: both; margin: 5px 0 10px 0;',
                    html: record.name + ': ' + t('coreshop_product_unit_definition_price_not_available')
                });
            }

        }.bind(this));

        return fieldSet;
    },

    getUnitDefinitionStorePrice: function (productUnitDefinitionPrices, definitionId) {

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
    }
});