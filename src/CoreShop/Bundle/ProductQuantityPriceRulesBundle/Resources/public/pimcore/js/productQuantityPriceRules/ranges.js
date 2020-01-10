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

pimcore.registerNS('coreshop.product_quantity_price_rules.ranges');
coreshop.product_quantity_price_rules.ranges = Class.create({

    internalTmpId: null,
    ruleId: null,
    objectId: null,
    clipboardManager: null,
    pricingBehaviourTypes: [],
    storeDataChanged: false,

    initialize: function (ruleId, objectId, clipboardManager, pricingBehaviourTypes) {
        this.storeDataChanged = false;
        this.internalTmpId = Ext.id();
        this.ruleId = ruleId;
        this.objectId = objectId;
        this.clipboardManager = clipboardManager;
        this.pricingBehaviourTypes = pricingBehaviourTypes;

        this.afterInitialization();
    },

    afterInitialization: function () {
        // keep it for 3rd party modifiers.
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

        var columns = this.generateGridColumns(data),
            gridPanel = this.generateGridPanel(columns, data);

        this.rangesContainer.add(gridPanel);
        this.rangesContainer.updateLayout();

        this.checkClipboard();
        this.afterRangesAdded(columns, gridPanel);
    },

    afterRangesAdded: function (columns, gridPanel) {
        // keep it for 3rd party modifiers.
    },

    getRangesData: function () {

        var grid = this.rangesContainer.query('[name=price-rule-ranges-grid]')[0],
            ranges = [];

        grid.getStore().each(function (record) {
            ranges.push({
                'id': record.get('rangeId'),
                'rangeStartingFrom': record.get('rangeStartingFrom'),
                'pricingBehaviour': record.get('pricingBehaviour'),
                'highlighted': record.get('highlighted'),
            });
        });

        return ranges;
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

        if (this.storeDataChanged === true) {
            return true;
        }

        if (store.getModifiedRecords().length > 0 || store.getNewRecords().length > 0 || store.getRemovedRecords().length > 0) {
            return true;
        }

        return false;
    },

    generateGridColumns: function (data) {

        var columns;

        columns = [
            {
                flex: 1,
                sortable: false,
                dataIndex: 'rangeId',
                hideable: false,
                hidden: true
            },
            {
                text: t('coreshop_product_quantity_price_rules_range_starting_from'),
                flex: 1,
                sortable: false,
                readOnly: true,
                dataIndex: 'rangeStartingFrom',
                name: 'quantity_range_starting_from',
                getEditor: function () {
                    return new Ext.form.NumberField({
                        minValue: 0,
                        decimalPrecision: 0,
                        step: 1,
                        listeners: {
                            render: this.onRangeStartingFromRender.bind(this)
                        }
                    });
                }.bind(this),
                renderer: function (value) {
                    if (value === undefined || value === null) {
                        return '0' + ' ' + t('coreshop_product_quantity_price_rules_quantity_amount');
                    }
                    return value + ' ' + t('coreshop_product_quantity_price_rules_quantity_amount');
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
                        store: this.pricingBehaviourTypes,
                        listeners: {
                            change: this.onPriceBehaviourChange.bind(this),
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
                }.bind(this),
                renderer: function (value) {
                    if (value === undefined || value === null) {
                        return t('coreshop_product_quantity_price_rules_behaviour_nothing_selected');
                    }
                    return t('coreshop_product_quantity_price_rules_behaviour_' + value);
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
                menuText: t('up'),
                width: 30,
                items: [
                    {
                        tooltip: t('up'),
                        icon: '/bundles/pimcoreadmin/img/flat-color-icons/up.svg',
                        handler: function (grid, rowIndex) {
                            if (rowIndex > 0) {
                                var rec = grid.getStore().getAt(rowIndex);
                                grid.getStore().removeAt(rowIndex);
                                grid.getStore().insert(rowIndex - 1, [rec]);
                            }
                        }.bind(this)
                    }
                ]
            },
            {
                xtype: 'actioncolumn',
                menuText: t('down'),
                width: 30,
                items: [
                    {
                        tooltip: t('down'),
                        icon: '/bundles/pimcoreadmin/img/flat-color-icons/down.svg',
                        handler: function (grid, rowIndex) {
                            if (rowIndex < (grid.getStore().getCount() - 1)) {
                                var rec = grid.getStore().getAt(rowIndex);
                                grid.getStore().removeAt(rowIndex);
                                grid.getStore().insert(rowIndex + 1, [rec]);
                            }
                        }.bind(this)
                    }
                ]
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

        return columns;
    },

    generateGridPanel: function (columns, storeData) {

        return new Ext.Panel({
            items: [
                {
                    xtype: 'grid',
                    enableDragDrop: true,
                    ddGroup: 'element',
                    name: 'price-rule-ranges-grid',
                    frame: false,
                    autoScroll: true,
                    store: new Ext.data.Store({
                        data: this.adjustRangeStoreData(storeData),
                        listeners: {
                            add: function () {
                                this.storeDataChanged = true;
                            }.bind(this),
                            remove: function () {
                                this.storeDataChanged = true;
                            }.bind(this),
                            clear: function () {
                                this.storeDataChanged = true;
                            }.bind(this),
                            update: function (store) {
                                if (store.ignoreDataChanged) {
                                    return;
                                }
                                this.storeDataChanged = true;
                            }.bind(this)
                        }
                    }),
                    columnLines: true,
                    stripeRows: true,
                    bodyCls: 'pimcore_editable_grid',
                    trackMouseOver: true,
                    columns: columns,
                    selModel: {
                        selType: 'rowmodel'
                    },
                    autoExpandColumn: 'value_col',
                    plugins: [
                        this.generateCellEditing()
                    ],
                    viewConfig: {
                        forceFit: true,
                        markDirty: false,
                        plugins: {
                            ptype: 'gridviewdragdrop',
                            draggroup: 'element'
                        },
                        listeners: {
                            drop: function (node, data, dropRec, dropPosition) {
                                // this is necessary to avoid endless recursion when lists are sorted via d&d
                                var panel = this.rangesContainer.up('panel'),
                                    items, subItems;

                                if (panel && panel.hasOwnProperty('items')) {
                                    items = panel.items;
                                }

                                if (items && items.hasOwnProperty('items')) {
                                    subItems = items.items;
                                }

                                if (subItems && subItems.length > 0) {
                                    subItems[0].focus();
                                }

                            }.bind(this),
                            // see https://github.com/pimcore/pimcore/issues/979
                            cellmousedown: function (element, td, cellIndex, record, tr, rowIndex, event) {
                                var el;
                                if (event.getTarget()) {
                                    el = Ext.fly(event.getTarget());
                                    if (el && el.hasOwnProperty('id')) {
                                        return false;
                                    }
                                }
                                return true;
                            }
                        }
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
    },

    generateCellEditing: function () {
        return Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1,
            listeners: {
                beforeedit: function (editor, context) {
                    var record = context.record;

                    if (this.cellEditingIsAllowed(record, context.column.name) === false) {
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
                }.bind(this)
            }
        });
    },

    cellEditingIsAllowed: function (record, currentColumnName) {
        return true;
    },

    onRangeStartingFromRender: function (field) {
        // keep it for 3rd party modifiers.
    },

    onPriceBehaviourChange: function (field) {
        // keep it for 3rd party modifiers.
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

        this.clipboardManager.addData('quantityPriceRange', {
            id: this.internalTmpId,
            records: grid.getStore().getData().items
        });

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
            newEntry;

        newEntry = this.parseNewModelClass(grid);

        grid.getStore().add(newEntry);
        this.checkClipboard();
        grid.getView().refresh();

    },

    parseNewModelClass: function (grid) {

        var modelClass = grid.getStore().getModel(),
            lastEntry = grid.getStore().last();

        return new modelClass({
            rangeStartingFrom: lastEntry !== null ? lastEntry.get('rangeStartingFrom') + 10 : 0,
            pricingBehaviour: 'fixed',
            highlight: false,
            rangeId: null
        });

    },

    adjustRangeStoreData: function (data) {

        if (!Ext.isArray(data)) {
            return [];
        }

        Ext.Array.each(data, function (range, key) {
            if (range.hasOwnProperty('id')) {
                data[key]['rangeId'] = range['id'];
                delete data[key]['id'];
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
