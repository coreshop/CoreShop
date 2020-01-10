/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

coreshop.product_quantity_price_rules.ranges = Class.create(coreshop.product_quantity_price_rules.ranges, {

    productUnitDefinitionsStore: null,
    amountBasedBehaviour: ['fixed', 'amount_decrease', 'amount_increase'],
    percentBasedBehaviour: ['percentage_decrease', 'percentage_increase'],

    afterInitialization: function () {

        var unitDefinitionModelName, proxy;

        unitDefinitionModelName = 'coreshop.product.model.productUnitDefinitions';
        proxy = {
            type: 'ajax',
            url: '/admin/coreshop/product_unit_definitions/get-product-unit-definitions',
            extraParams: {
                productId: this.objectId
            },
            actionMethods: {
                read: 'GET'
            },
            reader: {
                type: 'json'
            }
        };

        if (!Ext.ClassManager.get(unitDefinitionModelName)) {
            Ext.define(unitDefinitionModelName, {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id'},
                    {name: 'unit'},
                    {name: 'unitName', mapping: 'unit.name'},
                    {name: 'unitLabel', mapping: 'unit.fullLabel'},
                ]
            });
        }

        this.productUnitDefinitionsStore = new Ext.data.Store({
            model: unitDefinitionModelName,
            proxy: proxy
        });

    },

    afterRangesAdded: function (columns, gridPanel) {

        var grid = this.rangesContainer.query('[name=price-rule-ranges-grid]')[0],
            unitDefinitionColumn = grid.columnManager.getHeaderByDataIndex('unitDefinition');

        if (this.productUnitDefinitionsStore.isLoaded()) {
            if (this.productUnitDefinitionsStore.getRange().length > 0) {
                unitDefinitionColumn.show();
            }
        } else {
            this.productUnitDefinitionsStore.load(function (data) {
                if (Ext.isArray(data) && data.length > 0) {
                    unitDefinitionColumn.show();
                }
            }.bind(this));
        }
    },

    getRangesData: function () {

        var grid = this.rangesContainer.query('[name=price-rule-ranges-grid]')[0],
            ranges = [];

        grid.getStore().each(function (record) {
            ranges.push({
                'id': record.get('rangeId'),
                'rangeStartingFrom': record.get('rangeStartingFrom'),
                'pricingBehaviour': record.get('pricingBehaviour'),
                'unitDefinition': record.get('unitDefinition'),
                'amount': record.get('amount'),
                'currency': record.get('currency'),
                'percentage': record.get('percentage'),
                'pseudoPrice': record.get('pseudoPrice'),
                'highlighted': record.get('highlighted'),
            });
        });

        return ranges;
    },

    cloneStore: function (store) {
        var records = [];
        store.each(function (r) {
            records.push(r.copy());
        });

        var store2 = new Ext.data.Store({
            recordType: store.recordType
        });

        store2.add(records);
        store2.insert(0, {
            name: t('empty'),
            id: null
        });

        return store2;
    },

    generateGridColumns: function ($super, data) {

        var _ = this, columns, additionalColumns, currencyBaseStore, rangeCurrencyStore;

        columns = $super(data);

        currencyBaseStore = pimcore.globalmanager.get('coreshop_currencies');

        if (currencyBaseStore.isLoaded()) {
            rangeCurrencyStore = this.cloneStore(currencyBaseStore);
        } else {
            currencyBaseStore.load(function (store) {
                rangeCurrencyStore = this.cloneStore(store);
            }.bind(this));
        }

        additionalColumns = {
            'unitDefinition': [{
                text: t('coreshop_product_quantity_price_rules_unit_definition'),
                flex: 1,
                sortable: false,
                dataIndex: 'unitDefinition',
                name: 'unit_definition',
                hidden: true,
                hideable: false,
                getEditor: function () {
                    return new Ext.form.ComboBox({
                        store: _.productUnitDefinitionsStore,
                        valueField: 'id',
                        displayField: 'unitLabel',
                        allowBlank: false,
                        editable: false,
                        queryMode: 'local',
                        triggerAction: 'all',
                        listeners: {
                            select: function (combo) {
                                var grid = this.up('grid'),
                                    selectedModel = grid.getSelectionModel().getSelected().getAt(0);

                                selectedModel.set('unitDefinition', combo.getValue());
                                combo.up('editor').completeEdit(true);
                                combo.up('grid').getView().refresh();
                            }
                        }
                    });
                },
                renderer: function (unitDefinitionId) {
                    var unitDefinitionRecord;
                    if (unitDefinitionId === undefined || unitDefinitionId === null) {
                        return '--';
                    }

                    unitDefinitionRecord = _.productUnitDefinitionsStore.getById(unitDefinitionId);

                    if(unitDefinitionRecord) {
                        return unitDefinitionRecord.get('unitLabel');
                    }

                    return '-- (Id: ' + unitDefinitionId + ')';
                }
            }],
            'quantityAmount': [{
                text: t('coreshop_product_quantity_price_rules_amount'),
                flex: 1,
                sortable: false,
                dataIndex: 'amount',
                name: 'quantity_amount',
                getEditor: function () {
                    return new Ext.form.NumberField({
                        minValue: 0
                    });
                },
                renderer: function (value, cell, record) {
                    var prefix = '';
                    if (record.get('pricingBehaviour') === 'amount_increase') {
                        prefix = '+';
                    } else if (record.get('pricingBehaviour') === 'amount_decrease') {
                        prefix = '-';
                    }

                    if (_.isInArray(record.get('pricingBehaviour'), _.percentBasedBehaviour)) {
                        _.setDisabledStyleForCell(cell);
                    }

                    if (value === undefined) {
                        return coreshop.util.format.currency('', 0);
                    } else {
                        return prefix + coreshop.util.format.currency('', parseFloat(value) * pimcore.globalmanager.get('coreshop.currency.decimal_factor'));
                    }
                }
            }],
            'currency': [{
                text: t('coreshop_product_quantity_price_rules_currency'),
                flex: 1,
                sortable: false,
                dataIndex: 'currency',
                name: 'currency',
                getEditor: function () {
                    return new Ext.form.ComboBox({
                        store: rangeCurrencyStore,
                        valueField: 'id',
                        displayField: 'name',
                        queryMode: 'local',
                        allowBlank: true
                    });
                },
                renderer: function (currencyId, cell, record) {
                    var store, currencyObject;

                    if (_.isInArray(record.get('pricingBehaviour'), _.percentBasedBehaviour)) {
                        _.setDisabledStyleForCell(cell);
                    }

                    store = pimcore.globalmanager.get('coreshop_currencies');
                    currencyObject = store.getById(currencyId);
                    if (currencyObject) {
                        return currencyObject.get('name');
                    }

                    return t('empty');
                }
            }],
            'quantityPercentage': [{
                text: t('coreshop_product_quantity_price_rules_percentage'),
                flex: 1,
                sortable: false,
                dataIndex: 'percentage',
                name: 'quantity_percentage',
                getEditor: function () {
                    return new Ext.form.NumberField({
                        minValue: 0,
                        maxValue: 100
                    });
                }.bind(this),
                renderer: function (value, cell, record) {
                    var prefix = '';
                    if (record.get('pricingBehaviour') === 'percentage_increase') {
                        prefix = '+';
                    } else if (record.get('pricingBehaviour') === 'percentage_decrease') {
                        prefix = '-';
                    }

                    if (_.isInArray(record.get('pricingBehaviour'), _.amountBasedBehaviour)) {
                        _.setDisabledStyleForCell(cell);
                    }

                    if (value !== undefined) {
                        return prefix + value + '%';
                    }
                    return '--';
                }
            }],
            'pseudoPrice': [{
                text: t('coreshop_product_quantity_price_rules_pseudo_price'),
                flex: 1,
                sortable: false,
                dataIndex: 'pseudoPrice',
                name: 'pseudo_price',
                getEditor: function () {
                    return new Ext.form.NumberField({
                        minValue: 0
                    });
                },
                renderer: function (value, cell, record) {

                    if (_.isInArray(record.get('pricingBehaviour'), _.percentBasedBehaviour)) {
                        _.setDisabledStyleForCell(cell);
                    }

                    if (value === undefined) {
                        return coreshop.util.format.currency('', 0);
                    } else {

                        return coreshop.util.format.currency('', parseFloat(value) * pimcore.globalmanager.get('coreshop.currency.decimal_factor'));
                    }
                }
            }]
        };

        columns = Ext.Array.insert(columns, 3, additionalColumns['unitDefinition']);
        columns = Ext.Array.insert(columns, 5, additionalColumns['quantityAmount']);
        columns = Ext.Array.insert(columns, 6, additionalColumns['currency']);
        columns = Ext.Array.insert(columns, 7, additionalColumns['quantityPercentage']);
        columns = Ext.Array.insert(columns, 8, additionalColumns['pseudoPrice']);

        return columns;
    },

    cellEditingIsAllowed: function ($super, record, currentColumnName) {

        var parentCellEditingIsAllowed = $super(record, currentColumnName);

        if (parentCellEditingIsAllowed === false) {
            return false;
        }

        if (this.isInArray(currentColumnName, ['quantity_amount', 'currency', 'pseudo_price'])
            && this.isInArray(record.get('pricingBehaviour'), this.percentBasedBehaviour)) {
            return false;
        } else if (currentColumnName === 'quantity_percentage'
            && this.isInArray(record.get('pricingBehaviour'), this.amountBasedBehaviour)) {
            return false;
        }

        return true;
    },

    onRangeStartingFromRender: function ($super, field) {

        var grid = field.up('grid'),
            selectedModel = grid.getSelectionModel().getSelected().getAt(0),
            unitDefinitionId,
            unitDefinitionRecord,
            precision, step = 1;

        $super();

        if (!selectedModel) {
            return;
        }

        unitDefinitionId = selectedModel.get('unitDefinition');
        unitDefinitionRecord = this.productUnitDefinitionsStore.getById(unitDefinitionId);

        if (!unitDefinitionRecord) {
            return;
        }

        precision = unitDefinitionRecord.get('precision');
        if (isNaN(precision)) {
            return;
        }

        if (precision > 0) {
            step = (1 / parseInt('1' + (Ext.String.repeat('0', precision))));
        }

        field.decimalPrecision = precision;
        field.step = step;
    },

    onPriceBehaviourChange: function ($super, field) {

        var grid = field.up('grid'),
            selectedModel = grid.getSelectionModel().getSelected().getAt(0);

        $super();

        if (this.isInArray(field.getValue(), this.percentBasedBehaviour)) {
            selectedModel.set('amount', 0);
            selectedModel.set('pseudoPrice', 0);
            selectedModel.set('currency', null);
        } else if (this.isInArray(field.getValue(), this.amountBasedBehaviour)) {
            selectedModel.set('percentage', 0);
        }
    },

    parseNewModelClass: function ($super, grid) {

        var modelClass = $super(grid);

        var lastEntry = grid.getStore().last(),
            lastUnit = null;

        if (lastEntry !== null) {
            lastUnit = lastEntry.get('unitDefinition')
        } else if (this.productUnitDefinitionsStore.getRange().length === 1) {
            lastUnit = this.productUnitDefinitionsStore.first().get('id');
        }

        modelClass.set('amount', 0);
        modelClass.set('unitDefinition', lastUnit);
        modelClass.set('currency', lastEntry !== null ? lastEntry.get('currency') : null);
        modelClass.set('percentage', 0);
        modelClass.set('pseudoPrice', 0);

        return modelClass;

    },

    adjustRangeStoreData: function ($super, rawData) {

        var data = $super(rawData);

        if (!Ext.isArray(data)) {
            return [];
        }

        Ext.Array.each(data, function (range, key) {
            var p;

            if (range.hasOwnProperty('unitDefinition') && Ext.isObject(range.unitDefinition)) {
                data[key]['unitDefinition'] = range.unitDefinition.id;
            }

            if (range.hasOwnProperty('currency') && Ext.isObject(range.currency)) {
                data[key]['currency'] = range.currency.id;
            }

            if (range.hasOwnProperty('amount')) {
                p = parseInt(range['amount']);
                if (p > 0) {
                    data[key]['amount'] = parseInt(range['amount']) / pimcore.globalmanager.get('coreshop.currency.decimal_factor');
                }
            }

            if (range.hasOwnProperty('pseudoPrice')) {
                p = parseInt(range['pseudoPrice']);
                if (p > 0) {
                    data[key]['pseudoPrice'] = parseInt(range['pseudoPrice']) / pimcore.globalmanager.get('coreshop.currency.decimal_factor');
                }
            }
        });

        return data;
    }

});
