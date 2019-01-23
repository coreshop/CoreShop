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

pimcore.registerNS('coreshop.tier_pricing.specific_tier_price.ranges');
coreshop.tier_pricing.specific_tier_price.ranges = Class.create({

    ruleId: null,
    initialize: function (ruleId) {
        this.ruleId = ruleId;
    },

    postSaveObject: function (object, refreshedData, task, fieldName) {
        if (this.isDirty()) {
            this.remapTierPriceIds(refreshedData);
        } else {
            this.commitStoreChanges();
        }
    },

    remapTierPriceIds: function (refreshedData) {

        var grid = this.rangesContainer.query('[name=tier-price-grid]')[0];

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
        var grid = this.rangesContainer.query('[name=tier-price-grid]')[0];
        grid.getStore().commitChanges();
    },

    getLayout: function () {

        this.rangesContainer = new Ext.Panel({
            iconCls: 'coreshop_icon_tier_price',
            title: t('coreshop_tier_price_ranges'),
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
    },

    getRangesData: function () {
        // get defined ranges
        var grid = this.rangesContainer.query('[name=tier-price-grid]')[0],
            ranges = [];

        grid.getStore().each(function (record) {
            ranges.push({
                'id': record.get('rangeId'),
                'rangeFrom': record.get('rangeFrom'),
                'rangeTo': record.get('rangeTo'),
                'price': record.get('price'),
                'percentageDiscount': record.get('percentageDiscount'),
                'highlighted': record.get('highlighted'),
            });
        });

        return ranges;
    },

    isDirty: function () {

        var grid = this.rangesContainer.query('[name=tier-price-grid]')[0],
            store = grid.getStore();

        if (store.getModifiedRecords().length > 0 || store.getNewRecords().length > 0 || store.getRemovedRecords().length > 0) {
            return true;
        }

        return false;
    },

    generateGrid: function (storeData, store) {

        var panel,
            columns = [
                {
                    flex: 1,
                    sortable: false,
                    dataIndex: 'rangeId',
                    hideable: false,
                    hidden: true
                },
                {
                    text: t('coreshop_tier_range_from'),
                    flex: 1,
                    sortable: false,
                    readOnly: true,
                    dataIndex: 'rangeFrom',
                    name: 'tier_range_from',
                    getEditor: function () {
                        return new Ext.form.NumberField({
                            minValue: 0
                        });
                    },
                    renderer: function (value) {
                        if (value === undefined || value === null) {
                            return '0' + ' ' + t('coreshop_tier_quantity_amount');
                        }
                        return value + ' ' + t('coreshop_tier_quantity_amount');
                    }
                },
                {
                    text: t('coreshop_tier_range_to'),
                    flex: 1,
                    sortable: false,
                    dataIndex: 'rangeTo',
                    name: 'tier_range_to',
                    getEditor: function () {
                        return new Ext.form.NumberField({
                            minValue: 1
                        });
                    },
                    renderer: function (value) {
                        if (value === undefined || value === null) {
                            return '0' + ' ' + t('coreshop_tier_quantity_amount');
                        }
                        return value + ' ' + t('coreshop_tier_quantity_amount');
                    }
                },
                {
                    text: t('coreshop_tier_new_price'),
                    flex: 1,
                    sortable: false,
                    dataIndex: 'price',
                    name: 'tier_price',
                    getEditor: function () {
                        return new Ext.form.NumberField({
                            minValue: 0
                        });
                    },
                    renderer: function (value, d) {
                        if (value === undefined) {
                            // @todo: find currency (from currency row / selector (?)
                            return coreshop.util.format.currency('', 0);
                        } else {
                            d.tdStyle = value === 0 ? 'color: grey; font-style: italic;' : '';
                            return coreshop.util.format.currency('', parseFloat(value) * 100);
                        }
                    }
                },
                {
                    text: t('coreshop_tier_percentage_discount'),
                    flex: 1,
                    sortable: false,
                    dataIndex: 'percentageDiscount',
                    name: 'tier_percentage_discount',
                    getEditor: function () {
                        return new Ext.form.NumberField({
                            minValue: 0,
                            maxValue: 100
                        });
                    }.bind(this),
                    renderer: function (value, d) {
                        if (value !== undefined) {
                            d.tdStyle = value === 0 ? 'color: grey; font-style: italic;' : '';
                            return value + '%';
                        }
                        return '--';
                    }
                },
                {
                    text: t('coreshop_tier_highlight'),
                    flex: 1,
                    sortable: false,
                    dataIndex: 'highlighted',
                    name: 'tier_highlight',
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
                        }.bind(this)
                    }]
                }
            ];

        this.cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1,
            listeners: {
                edit: function (editor, context) {
                    var record = context.record;
                    if (context.column.name === 'tier_price') {
                        record.set('percentageDiscount', 0);
                    } else if (context.column.name === 'tier_percentage_discount') {
                        record.set('price', 0);
                    }
                }.bind(this),
                beforeedit: function (editor) {
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
                    name: 'tier-price-grid',
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
                        this.cellEditing
                    ],
                    viewConfig: {
                        forceFit: true
                    },
                    tbar: [{
                        text: t('add'),
                        handler: this.onAdd,
                        iconCls: 'pimcore_icon_add'
                    }]
                }
            ]
        });

        return panel;

    },

    onAdd: function (btn) {
        var grid = btn.up('grid'),
            modelClass = grid.getStore().getModel(),
            lastEntry = grid.getStore().last(),
            newEntry;

        newEntry = new modelClass({
            rangeFrom: lastEntry !== null ? lastEntry.get('rangeTo') + 1 : 0,
            rangeTo: lastEntry !== null ? lastEntry.get('rangeTo') + 10 : 10,
            percentageDiscount: 0,
            price: 0,
            rangeId: null
        });

        grid.getStore().add(newEntry);
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
            if (range.hasOwnProperty('price')) {
                p = parseInt(range['price']);
                if (p > 0) {
                    data[key]['price'] = parseInt(range['price']) / 100;
                }
            }
        });

        return data;
    }
});
