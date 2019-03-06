/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.product_quantity_price_rules.ranges');
coreshop.product_quantity_price_rules.ranges = Class.create({

    internalTmpId: null,
    ruleId: null,
    clipboardManager: null,
    unitStore: null,

    amountBasedBehaviour: ['fixed', 'amount_decrease', 'amount_increase'],
    percentBasedBehaviour: ['percentage_decrease', 'percentage_increase'],

    initialize: function (ruleId, clipboardManager) {
        this.internalTmpId = Ext.id();
        this.ruleId = ruleId;
        this.clipboardManager = clipboardManager;
        this.unitStore = pimcore.globalmanager.get('coreshop_product_units');
    },

    postSaveObject: function (object, refreshedData) {
        if (this.isDirty()) {
            this.remapProductQuantityPriceRuleIds(refreshedData);
        } else {
            this.commitStoreChanges();
        }
    },

    remapProductQuantityPriceRuleIds: function (refreshedData) {

        var grid = this.rangesContainer.query('[name=price-rule-ranges-grid]')[0];

        if (this.ruleId === null || refreshedData === null) {
            this.commitStoreChanges();
            return;
        }

        this.rangesContainer.setLoading(true);
        if (refreshedData.hasOwnProperty('ranges') && Ext.isArray(refreshedData.ranges)) {
            grid.getStore().setData(this.adjustRangeStoreData(refreshedData.ranges));
        }

        this.rangesContainer.setLoading(false);
        this.commitStoreChanges();
    },

    commitStoreChanges: function () {
        var grid = this.rangesContainer.query('[name=price-rule-ranges-grid]')[0];
        grid.getStore().commitChanges();
    },

    getLayout: function () {

        this.rangesContainer = new Ext.Panel({
            iconCls: 'coreshop_icon_product_quantity_price_rules',
            title: t('coreshop_product_quantity_price_rules_ranges'),
            autoScroll: true,
            forceLayout: true,
            style: 'padding: 10px',
            border: false
        });

        return this.rangesContainer;
    },

    destroy: function () {
        if (this.rangesContainer) {
            this.rangesContainer.destroy();
        }
    },

    addRanges: function (data) {
        this.rangesContainer.add(this.generateGrid(data));
        this.rangesContainer.updateLayout();
        this.checkClipboard();
        this.checkUnitAvailability();
    },

    getRangesData: function () {
        // get defined ranges
        var grid = this.rangesContainer.query('[name=price-rule-ranges-grid]')[0],
            ranges = [];


        grid.getStore().each(function (record) {
            var currencyId = null,
                currencRecord = record.get('currency');
            if (!isNaN(currencRecord)) {
                currencyId = currencRecord;
            } else if (Ext.isObject(currencRecord) && currencRecord.hasOwnProperty('id')) {
                currencyId = currencRecord.id;
            }

            ranges.push({
                'id': record.get('rangeId'),
                'rangeFrom': record.get('rangeFrom'),
                'rangeTo': record.get('rangeTo'),
                'pricingBehaviour': record.get('pricingBehaviour'),
                'unitDefinition': record.get('unitDefinition'),
                'amount': record.get('amount'),
                'currency': currencyId,
                'percentage': record.get('percentage'),
                'pseudoPrice': record.get('pseudoPrice'),
                'highlighted': record.get('highlighted'),
            });
        });

        return ranges;
    },

    checkUnitAvailability: function () {

        var grid = this.rangesContainer.query('[name=price-rule-ranges-grid]')[0],
            unitDefinitionColumn = grid.columnManager.getHeaderByDataIndex('unitDefinition');

        if (this.unitStore.isLoaded()) {
            if (this.unitStore.getRange().length > 0) {
                unitDefinitionColumn.show();
            }
        } else {
            this.unitStore.load(function (store) {
                if (store.getRange().length > 0) {
                    unitDefinitionColumn.show();
                }
            }.bind(this));
        }
    },

    resetDeepId: function () {
        var grid = this.rangesContainer.query('[name=price-rule-ranges-grid]')[0];
        grid.getStore().each(function (record) {
            record.set('rangeId', null);
        });
    },

    isDirty: function () {

        var grid = this.rangesContainer.query('[name=price-rule-ranges-grid]')[0],
            store = grid.getStore();

        if (store.getModifiedRecords().length > 0 || store.getNewRecords().length > 0 || store.getRemovedRecords().length > 0) {
            return true;
        }

        return false;
    },

    generateGrid: function (storeData, store) {

        var _ = this, currencyBaseStore, rangeCurrencyStore, panel, columns, cellEditing,
            cloneStore = function (store) {
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
            };

        currencyBaseStore = pimcore.globalmanager.get('coreshop_currencies');

        if (currencyBaseStore.isLoaded()) {
            rangeCurrencyStore = cloneStore(currencyBaseStore);
        } else {
            currencyBaseStore.load(function (store) {
                rangeCurrencyStore = cloneStore(store);
            }.bind(this));
        }

        columns = [
            {
                flex: 1,
                sortable: false,
                dataIndex: 'rangeId',
                hideable: false,
                hidden: true
            },
            {
                text: t('coreshop_product_quantity_price_rules_range_from'),
                flex: 1,
                sortable: false,
                readOnly: true,
                dataIndex: 'rangeFrom',
                name: 'quantity_range_from',
                getEditor: function () {
                    return new Ext.form.NumberField({
                        minValue: 0
                    });
                },
                renderer: function (value) {
                    if (value === undefined || value === null) {
                        return '0' + ' ' + t('coreshop_product_quantity_price_rules_quantity_amount');
                    }
                    return value + ' ' + t('coreshop_product_quantity_price_rules_quantity_amount');
                }
            },
            {
                text: t('coreshop_product_quantity_price_rules_range_to'),
                flex: 1,
                sortable: false,
                dataIndex: 'rangeTo',
                name: 'quantity_range_to',
                getEditor: function () {
                    return new Ext.form.NumberField({
                        minValue: 1
                    });
                },
                renderer: function (value, cell, record, rowIndex) {
                    var lastElement = record.store.getRange().length === (rowIndex + 1);
                    if (value === undefined || value === null) {
                        return '0' + ' ' + t('coreshop_product_quantity_price_rules_quantity_amount');
                    }
                    return value + ' ' + t('coreshop_product_quantity_price_rules_quantity_amount') + (lastElement === true ? '+' : '');
                }
            },
            {
                text: t('coreshop_product_quantity_price_rules_unit_definition'),
                flex: 1,
                sortable: false,
                dataIndex: 'unitDefinition',
                name: 'unit_definition',
                hidden: true,
                hideable: false,
                getEditor: function () {
                    return new Ext.form.ComboBox({
                        store: _.unitStore,
                        valueField: 'id',
                        displayField: 'name',
                        queryMode: 'local',
                        allowBlank: false,
                        editable: false,
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
                renderer: function (value) {
                    var unitRecord;
                    if (value === undefined || value === null) {
                        return '--';
                    }
                    unitRecord = _.unitStore.getById(value);
                    return unitRecord.get('name');
                }
            },
            {
                text: t('coreshop_product_quantity_price_rules_behaviour'),
                flex: 1,
                sortable: false,
                dataIndex: 'pricingBehaviour',
                name: 'pricing_behaviour',
                getEditor: function () {
                    return new Ext.form.ComboBox({
                        store: [
                            ['fixed', t('coreshop_product_quantity_price_rules_behaviour_fixed')],
                            ['amount_decrease', t('coreshop_product_quantity_price_rules_behaviour_amount_decrease')],
                            ['amount_increase', t('coreshop_product_quantity_price_rules_behaviour_amount_increase')],
                            ['percentage_decrease', t('coreshop_product_quantity_price_rules_behaviour_percentage_decrease')],
                            ['percentage_increase', t('coreshop_product_quantity_price_rules_behaviour_percentage_increase')]
                        ],
                        listeners: {
                            change: function (field) {
                                var grid = this.up('grid'),
                                    selectedModel = grid.getSelectionModel().getSelected().getAt(0);

                                if (_.isInArray(field.getValue(), _.percentBasedBehaviour)) {
                                    selectedModel.set('amount', 0);
                                    selectedModel.set('pseudoPrice', 0);
                                    selectedModel.set('currency', null);
                                } else if (_.isInArray(field.getValue(), _.amountBasedBehaviour)) {
                                    selectedModel.set('percentage', 0);
                                }
                            },
                            select: function (combo) {
                                var grid = this.up('grid'),
                                    selectedModel = grid.getSelectionModel().getSelected().getAt(0);

                                selectedModel.set('pricingBehaviour', combo.getValue());
                                combo.up('editor').completeEdit(true);
                                combo.up('grid').getView().refresh();
                            }
                        },
                        triggerAction: 'all',
                        editable: false,
                        queryMode: 'local'
                    });
                },
                renderer: function (value) {
                    if (value === undefined || value === null) {
                        return t('coreshop_product_quantity_price_rules_behaviour_nothing_selected');
                    }
                    return t('coreshop_product_quantity_price_rules_behaviour_' + value);
                }
            },
            {
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
                        return prefix + coreshop.util.format.currency('', parseFloat(value) * 100);
                    }
                }
            },
            {
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
                renderer: function (currency, cell, record) {
                    var store, currencyObject;

                    if (_.isInArray(record.get('pricingBehaviour'), _.percentBasedBehaviour)) {
                        _.setDisabledStyleForCell(cell);
                    }

                    if (!isNaN(currency)) {
                        store = pimcore.globalmanager.get('coreshop_currencies');
                        currencyObject = store.getById(currency);
                        if (currencyObject) {
                            return currencyObject.get('name');
                        }
                    } else if (Ext.isObject(currency) && currency.hasOwnProperty('name')) {
                        return currency.name;
                    }

                    return t('empty');
                }
            },
            {
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
            },
            {
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

                        return coreshop.util.format.currency('', parseFloat(value) * 100);
                    }
                }
            },
            {
                text: t('coreshop_product_quantity_price_rules_highlight'),
                flex: 1,
                sortable: false,
                dataIndex: 'highlighted',
                name: 'quantity_highlight',
                getEditor: function () {
                    return new Ext.form.Checkbox({});
                },
                renderer: function (value) {
                    if (value !== undefined) {
                        return value === true ? t('yes') : t('no');
                    }
                    return t('no');
                }
            },
            {
                xtype: 'actioncolumn',
                menuText: t('delete'),
                width: 40,
                items: [{
                    tooltip: t('delete'),
                    icon: '/bundles/pimcoreadmin/img/flat-color-icons/delete.svg',
                    handler: function (grid, rowIndex) {
                        grid.getStore().removeAt(rowIndex);
                        this.checkClipboard();
                        grid.up('grid').getView().refresh();
                    }.bind(this)
                }]
            }
        ];

        cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1,
            listeners: {
                beforeedit: function (editor, context) {
                    var record = context.record;

                    if (_.isInArray(context.column.name, ['quantity_amount', 'currency', 'pseudo_price'])
                        && _.isInArray(record.get('pricingBehaviour'), _.percentBasedBehaviour)) {
                        return false;
                    } else if (context.column.name === 'quantity_percentage'
                        && _.isInArray(record.get('pricingBehaviour'), _.amountBasedBehaviour)) {
                        return false;
                    }

                    editor.editors.each(function (e) {
                        try {
                            e.completeEdit();
                            Ext.destroy(e);
                        } catch (exception) {
                            // fail silently.
                        }
                    });

                    editor.editors.clear();
                }
            }
        });

        panel = new Ext.Panel({
            items: [
                {
                    xtype: 'grid',
                    name: 'price-rule-ranges-grid',
                    frame: false,
                    autoScroll: true,
                    store: new Ext.data.Store({
                        data: this.adjustRangeStoreData(storeData)
                    }),
                    columnLines: true,
                    stripeRows: true,
                    bodyCls: 'pimcore_editable_grid',
                    trackMouseOver: true,
                    columns: columns,
                    clicksToEdit: 1,
                    selModel: Ext.create('Ext.selection.CellModel', {}),
                    autoExpandColumn: 'value_col',
                    plugins: [
                        cellEditing
                    ],
                    viewConfig: {
                        forceFit: true
                    },
                    tbar: [
                        {
                            text: t('add'),
                            handler: this.onAdd.bind(this),
                            iconCls: 'pimcore_icon_add'
                        },
                        {
                            text: t('coreshop_product_quantity_price_rules_copy_ranges'),
                            handler: this.onCopy.bind(this),
                            iconCls: 'pimcore_icon_copy',
                            name: 'clipboard-copy-btn'
                        },
                        {
                            text: t('coreshop_product_quantity_price_rules_paste_ranges'),
                            handler: this.onPaste.bind(this),
                            iconCls: 'pimcore_icon_paste',
                            name: 'clipboard-paste-btn'
                        }
                    ]
                }
            ]
        });

        return panel;

    },

    onClipboardUpdated: function () {
        this.checkClipboard();
    },

    checkClipboard: function () {

        var grid = this.rangesContainer.query('[name=price-rule-ranges-grid]')[0],
            copyBtn = this.rangesContainer.query('toolbar button[name=clipboard-copy-btn]')[0],
            pasteBtn = this.rangesContainer.query('toolbar button[name=clipboard-paste-btn]')[0],
            pasteBtnVisible = false, pasteBtnTooltipText, cbData;

        copyBtn.setVisible(grid.getStore().getRange().length > 0);

        if (this.clipboardManager.hasData('quantityPriceRange')) {
            cbData = this.clipboardManager.getData('quantityPriceRange');
            pasteBtnTooltipText = cbData.records.length + ' ' + t('coreshop_product_quantity_price_rules_paste_entry_amounts');
            if (cbData.id !== this.internalTmpId) {
                pasteBtnVisible = true;
            }
        } else {
            pasteBtnTooltipText = '--';
        }

        pasteBtn.setVisible(pasteBtnVisible);
        pasteBtn.setTooltip(pasteBtnTooltipText);
    },

    onCopy: function (btn) {

        this.rangesContainer.setLoading(true);
        var grid = this.rangesContainer.query('[name=price-rule-ranges-grid]')[0];

        if (grid.getStore().getRange().length === 0) {
            this.clipboardManager.removeData('quantityPriceRange');
            return;
        }

        this.clipboardManager.addData('quantityPriceRange', {id: this.internalTmpId, records: grid.getStore().getData().items});
        this.checkClipboard();

        setTimeout(function () {
            this.rangesContainer.setLoading(false);
        }.bind(this), 200);

    },

    onPaste: function (btn) {

        var grid = this.rangesContainer.query('[name=price-rule-ranges-grid]')[0],
            cbData;

        if (!this.clipboardManager.hasData('quantityPriceRange')) {
            return;
        }

        cbData = this.clipboardManager.getData('quantityPriceRange');
        Ext.Array.each(cbData.records, function (record) {
            var copy = record.copy(null);
            copy.set('rangeId', null);
            grid.getStore().add(copy);
        });

        this.checkClipboard();
    },

    onAdd: function (btn) {

        var grid = btn.up('grid'),
            modelClass = grid.getStore().getModel(),
            lastEntry = grid.getStore().last(),
            newEntry;

        newEntry = new modelClass({
            rangeFrom: lastEntry !== null ? lastEntry.get('rangeTo') + 1 : 0,
            rangeTo: lastEntry !== null ? lastEntry.get('rangeTo') + 10 : 10,
            pricingBehaviour: 'fixed',
            amount: 0,
            unitDefinition: lastEntry !== null ? lastEntry.get('unitDefinition') : null,
            currency: lastEntry !== null ? lastEntry.get('currency') : null,
            percentage: 0,
            pseudoPrice: 0,
            rangeId: null
        });

        grid.getStore().add(newEntry);
        this.checkClipboard();
        grid.getView().refresh();

    },

    adjustRangeStoreData: function (data) {

        if (!Ext.isArray(data)) {
            return [];
        }

        Ext.Array.each(data, function (range, key) {
            var p;
            if (range.hasOwnProperty('id')) {
                data[key]['rangeId'] = range['id'];
                delete data[key]['id'];
            }

            if (range.hasOwnProperty('amount')) {
                p = parseInt(range['amount']);
                if (p > 0) {
                    data[key]['amount'] = parseInt(range['amount']) / 100;
                }
            }

            if (range.hasOwnProperty('pseudoPrice')) {
                p = parseInt(range['pseudoPrice']);
                if (p > 0) {
                    data[key]['pseudoPrice'] = parseInt(range['pseudoPrice']) / 100;
                }
            }
        });

        return data;
    },

    setDisabledStyleForCell(cellMeta) {

        if (!cellMeta.hasOwnProperty('tdStyle')) {
            cellMeta.tdStyle = '';
        }

        cellMeta.tdStyle += ' color: #c1c1c1; font-style: italic;';
        cellMeta.tdStyle += ' background: #eaeaea; cursor: default !important;';
    },

    isInArray(key, heyStack) {
        return heyStack.indexOf(key) !== -1;
    }
});
