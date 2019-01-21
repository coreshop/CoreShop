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

pimcore.registerNS('pimcore.object.tags.coreShopTierPrice');
pimcore.object.tags.coreShopTierPrice = Class.create(pimcore.object.tags.abstract, {

    type: 'coreShopTierPrice',
    storeFields: {},
    productGlobalStorePrice: {},

    initialize: function (data, fieldConfig) {

        this.defaultValue = null;
        this.storeFields = {};

        if ((typeof data === 'undefined' || data === null) && fieldConfig.defaultValue) {
            data = fieldConfig.defaultValue;
            this.defaultValue = data;
        }

        this.data = data;
        this.fieldConfig = fieldConfig;
        this.eventDispatcherKey = pimcore.eventDispatcher.registerTarget(this.eventDispatcherKey, this);
    },

    getGridColumnEditor: function (field) {
        return false;
    },

    getGridColumnFilter: function (field) {
        return false;
    },

    postSaveObject: function (object, task) {
        if (object.id === this.object.id) {
            if (this.isDirty()) {
                this.remapTierPriceIds(object);
            } else {
                this.commitStoreChanges();
            }
        }
    },

    remapTierPriceIds: function (object) {

        this.component.setLoading(true);

        Ext.Ajax.request({
            url: '/admin/object/get',
            params: {id: object.id},
            ignoreErrors: true,
            success: function (response) {
                try {
                    var dataObject = Ext.decode(response.responseText);
                    if (!dataObject.hasOwnProperty('data') || !dataObject.data.hasOwnProperty(this.getName())) {
                        return;
                    }

                    var tierPrices = dataObject.data[this.getName()];
                    Ext.Object.each(this.storeFields, function (storeId, panel) {
                        var grid = panel.query('[name=tier-price-grid]')[0],
                            id = panel.query('[name=tier-price-id]')[0];

                        if (tierPrices.hasOwnProperty(storeId)) {
                            grid.getStore().setData(tierPrices[storeId]['ranges']);
                            id.setValue(tierPrices[storeId]['id']);
                        } else {
                            id.setValue(null);
                        }
                    });

                    this.commitStoreChanges();

                } catch (e) {
                    console.log(e);
                }

                this.component.setLoading(false);

            }.bind(this),
            failure: function () {
                this.component.setLoading(false);
            }.bind(this),
        });
    },

    commitStoreChanges: function () {
        Ext.Object.each(this.storeFields, function (storeId, panel) {
            var grid = panel.query('[name=tier-price-grid]')[0];
            grid.getStore().commitChanges();
        });
    },

    getLayoutEdit: function () {

        this.fieldConfig.datatype = 'layout';
        this.fieldConfig.fieldtype = 'panel';

        var _ = this,
            stores = pimcore.globalmanager.get('coreshop_stores').getRange(),
            panelConf = {
                monitorResize: true,
                cls: 'object_field',
                activeTab: 0,
                height: 'auto',
                items: [],
                deferredRender: true,
                forceLayout: true,
                hideMode: 'offsets',
                enableTabScroll: true
            },
            wrapperConfig = {
                border: false,
                layout: 'fit'
            };

        if (this.fieldConfig.width) {
            wrapperConfig.width = this.fieldConfig.width;
        }

        if (this.fieldConfig.region) {
            wrapperConfig.region = this.fieldConfig.region;
        }

        if (this.fieldConfig.title) {
            wrapperConfig.title = this.fieldConfig.title;
        }

        if (this.fieldConfig.height) {
            panelConf.height = this.fieldConfig.height;
            panelConf.autoHeight = false;
        }

        if (this.context.containerType === 'fieldcollection') {
            this.context.subContainerType = 'localizedfield';
        } else {
            this.context.containerType = 'localizedfield';
        }

        coreshop.broker.addListener('core.store_price.price_initialize', function (value, store, object) {
            if (object.id === this.object.id) {
                this.productGlobalStorePrice[store.getId()] = value;
                console.log(this.productGlobalStorePrice);
            }
        }, this);

        coreshop.broker.addListener('core.store_price.price_change', function (value, store, object) {
            if (object.id === this.object.id) {
                this.productGlobalStorePrice[store.getId()] = value;
            }
        }, this);

        Ext.Object.each(stores, function (key, store) {

            var storeData = this.data.hasOwnProperty(store.getId()) ? this.data[store.getId()] : [],
                item;

            this.storeFields[store.getId()] = _.generateGrid(storeData, store);

            item = {
                xtype: 'panel',
                border: false,
                autoScroll: true,
                padding: '10px',
                deferredRender: true,
                hideMode: 'offsets',
                items: this.storeFields[store.getId()]
            };

            item.iconCls = 'coreshop_icon_tier_price';
            item.title = store.get('name');

            if (this.fieldConfig.labelWidth) {
                item.labelWidth = this.fieldConfig.labelWidth;
            }

            panelConf.items.push(item);

        }.bind(this));

        wrapperConfig.items = [new Ext.TabPanel(panelConf)];
        wrapperConfig.border = true;
        wrapperConfig.style = 'margin-bottom: 10px';

        this.component = new Ext.Panel(wrapperConfig);
        this.component.updateLayout();

        this.component.on('destroy', function () {
            pimcore.eventDispatcher.unregisterTarget(this.eventDispatcherKey);
        }.bind(this));

        return this.component;
    },

    getLayoutShow: function () {
        this.component = this.getLayoutEdit(true);
        return this.component;
    },

    getValue: function () {
        var values = {};
        Ext.Object.each(this.storeFields, function (storeId, panel) {
            var grid = panel.query('[name=tier-price-grid]')[0],
                id = panel.query('[name=tier-price-id]')[0],
                ranges = [];

            grid.getStore().each(function (record) {
                ranges.push({
                    'tier_range_id': record.get('tier_range_id'),
                    'tier_range_from': record.get('tier_range_from'),
                    'tier_range_to': record.get('tier_range_to'),
                    'tier_price': record.get('tier_price'),
                    'tier_percentage_discount': record.get('tier_percentage_discount'),
                    'tier_highlight': record.get('tier_highlight'),
                });
            });

            values[storeId] = {
                'id': id.getValue() ? id.getValue() : null,
                'ranges': ranges
            };
        });

        return values;
    },

    getName: function () {
        return this.fieldConfig.name;
    },

    isInvalidMandatory: function () {
        if (!this.isRendered() && (!empty(this.getInitialData() || this.getInitialData() === 0))) {
            return false;
        } else if (!this.isRendered()) {
            return true;
        }

        return this.getValue();
    },

    isDirty: function () {

        var isDirty = false;
        if (this.defaultValue) {
            return true;
        }

        if (!this.isRendered()) {
            return false;
        }

        Ext.Object.each(this.storeFields, function (storeId, panel) {
            var grid = panel.query('[name=tier-price-grid]')[0],
                store = grid.getStore();
            if (store.getModifiedRecords().length > 0 || store.getNewRecords().length > 0 || store.getRemovedRecords().length > 0) {
                isDirty = true;
                return false;
            }
        });

        return isDirty;
    },

    generateGrid: function (storeData, store) {

        var panel,
            columns = [
                {
                    flex: 1,
                    sortable: false,
                    dataIndex: 'tier_range_id',
                    hideable: false,
                    hidden: true
                },
                {
                    text: t('coreshop_tier_range_from'),
                    flex: 1,
                    sortable: false,
                    readOnly: true,
                    dataIndex: 'tier_range_from',
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
                    dataIndex: 'tier_range_to',
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
                    dataIndex: 'tier_price',
                    name: 'tier_price',
                    getEditor: function () {
                        return new Ext.form.NumberField({
                            minValue: 0
                        });
                    },
                    renderer: function (value) {
                        if (value === undefined) {
                            return coreshop.util.format.currency(storeData['currencySymbol'], 0);
                        } else {
                            return coreshop.util.format.currency(storeData['currencySymbol'], parseFloat(value) * 100);
                        }
                    }
                },
                {
                    text: t('coreshop_tier_percentage_discount'),
                    flex: 1,
                    sortable: false,
                    dataIndex: 'tier_percentage_discount',
                    name: 'tier_percentage_discount',
                    getEditor: function () {

                        if (!this.productGlobalStorePrice.hasOwnProperty(store.getId()) || this.productGlobalStorePrice[store.getId()] === 0) {
                            return false;
                        }

                        return new Ext.form.NumberField({
                            minValue: 0,
                            maxValue: 100
                        });

                    }.bind(this),
                    renderer: function (value) {
                        if (value !== undefined) {
                            return value + '%';
                        }
                        return '--';
                    }
                },
                {
                    text: t('coreshop_tier_highlight'),
                    flex: 1,
                    sortable: false,
                    dataIndex: 'tier_highlight',
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
                        record.set('tier_percentage_discount', 0);
                    } else if (context.column.name === 'tier_percentage_discount') {
                        if (this.productGlobalStorePrice.hasOwnProperty(store.getId())) {
                            record.set('tier_price', (this.productGlobalStorePrice[store.getId()] / 100 * record.get('tier_percentage_discount')));
                        }
                    }
                }.bind(this),
                beforeedit: function (editor) {
                    editor.editors.each(function (e) {
                        try {
                            e.completeEdit();
                            Ext.destroy(e);
                        } catch (exception) {
                        }
                    });

                    editor.editors.clear();
                }
            }
        });

        panel = new Ext.Panel({
            items: [
                {
                    name: 'tier-price-id',
                    xtype: 'hiddenfield',
                    value: storeData['id'],
                    disabled: true,
                    fieldLabel: t('id')
                },
                {
                    xtype: 'grid',
                    name: 'tier-price-grid',
                    frame: false,
                    autoScroll: true,
                    store: new Ext.data.Store({
                        data: storeData['ranges'],
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
            name: t('new_definition'),
            tier_range_id: null,
            tier_range_from: lastEntry !== null ? lastEntry.get('tier_range_to') + 1 : 0,
            tier_range_to: lastEntry !== null ? lastEntry.get('tier_range_to') + 10 : 10,
            tier_percentage_discount: 0,
            tier_price: 0
        });

        grid.getStore().add(newEntry);
    }

});