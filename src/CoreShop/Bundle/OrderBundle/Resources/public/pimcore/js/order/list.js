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

pimcore.registerNS('coreshop.order.order.list');
coreshop.order.order.list = Class.create({
    type: 'order',
    search: null,
    grid: null,
    gridPaginator: null,
    gridConfig: {},
    store: null,
    contextMenuPlugin: null,
    columns: [],
    storeFields: [],

    initialize: function () {
        Ext.Ajax.request({
            url: '/admin/coreshop/order/get-folder-configuration',
            ignoreErrors: true,
            params: {
                saleType: this.type
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);
                this.gridConfig = data;
                this.setClassFolder()
            }.bind(this)
        });
    },

    activate: function () {
        var tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.setActiveItem('coreshop_' + this.type);
    },

    setupContextMenuPlugin: function () {
        this.contextMenuPlugin = new coreshop.pimcore.plugin.grid(
            'coreshop_order',
            function (id) {
                this.open(id);
            }.bind(this),
            [coreshop.class_map.coreshop.order],
            this.getGridPaginator()
        );
    },

    setClassFolder: function () {
        Ext.Ajax.request({
            url: '/admin/object/get-folder',
            params: {id: this.gridConfig.folderId},
            ignoreErrors: true,
            success: function (response) {
                var data = Ext.decode(response.responseText);

                // unlock order overview silently
                // since multiple user may want to have access to it at the same time
                if (typeof data.editlock === 'object') {
                    Ext.Ajax.request({
                        url: '/admin/element/unlock-element',
                        method: 'PUT',
                        params: {
                            id: data.editlock.cid,
                            type: 'object'
                        },
                        success: function () {
                            this.setClassFolder();
                        }.bind(this)
                    });
                } else {
                    this.prepareLayout(data);
                }
            }.bind(this)
        });
    },

    prepareLayout: function (data) {

        var folderClass = [];

        Ext.Array.each(data.classes, function (objectClass) {
            if (objectClass.name === this.gridConfig.className) {
                folderClass.push(objectClass);
            }
        }.bind(this));

        data.classes = folderClass;
        this.search = new pimcore.object.search({data: data, id: this.gridConfig.folderId}, 'folder');
        this.getLayout();
    },

    prepareConfig: function (columnConfig) {
        var me = this,
            gridColumns = [],
            storeModelFields = [];

        Ext.each(columnConfig, function (column) {
            var newColumn = column;
            var storeModelField = {
                name: column.dataIndex,
                type: column.type
            };

            newColumn.id = me.type + '_' + newColumn.dataIndex;
            newColumn.text = newColumn.text.split('|').map(function (string) {
                //text like [foo bar] won't be translated. just remove brackets.
                return string.match(/\[([^)]+)]/) ? string.replace(/\[|]/gi, '') : t(string);
            }).join(' ');

            if (newColumn.hasOwnProperty('renderAs')) {
                if (Ext.isFunction(this[newColumn.renderAs + 'Renderer'])) {
                    newColumn.renderer = this[newColumn.renderAs + 'Renderer'];
                }
            }

            if (newColumn.type === 'date') {
                newColumn.xtype = 'datecolumn';
                newColumn.format = t('coreshop_date_time_format');

                storeModelField.dateFormat = 'timestamp';
            }

            if (newColumn.type === 'integer' || newColumn.type === 'float') {
                newColumn.xtype = 'numbercolumn';
            }

            storeModelFields.push(storeModelField);
            gridColumns.push(newColumn);
        }.bind(this));

        this.columns = gridColumns;
        this.storeFields = storeModelFields;
    },

    getLayout: function () {
        if (!this.layout) {

            // create new panel
            this.layout = new Ext.Panel({
                id: 'coreshop_' + this.type,
                title: t('coreshop_' + this.type),
                iconCls: 'coreshop_icon_' + this.type + 's',
                border: false,
                layout: 'border',
                closable: true,
                items: this.getItems(),
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [
                        {
                            iconCls: 'coreshop_icon_' + this.type + '_create',
                            text: t('coreshop_' + this.type + '_create'),
                            handler: function () {
                                new coreshop.order[this.type].create.panel();
                            }.bind(this)
                        },
                        this.getQuickOrder()
                    ]
                }]
            });

            // add event listener
            this.layout.on('destroy', function () {
                pimcore.globalmanager.remove('coreshop_' + this.type);
            }.bind(this));

            // add panel to pimcore panel tabs
            var tabPanel = Ext.getCmp('pimcore_panel_tabs');
            tabPanel.add(this.layout);
            tabPanel.setActiveItem('coreshop_' + this.type);

            // update layout
            pimcore.layout.refresh();
        }

        return this.layout;
    },

    getItems: function () {
        return [this.getGrid()];
    },

    getQuickOrder: function () {

        var fieldSettings = {
            enableKeyEvents: false,
            fieldCls: 'input_drop_target',
            style: 'background-color:white;',
            readOnly: true,
            emptyText: t('coreshop_' + this.type + '_quick_open'),
            width: 300
        }, drag = new Ext.form.TextField(fieldSettings);

        drag.on('render', function (el) {

            new Ext.dd.DropZone(el.getEl(), {
                reference: drag,
                ddGroup: 'element',
                getTargetFromEvent: function (e) {
                    return this.reference.getEl();
                },

                onNodeOver: function (target, dd, e, data) {

                    var record = data.records[0],
                        data = record.data;

                    if (data.className == coreshop.class_map.coreshop[this.type]) {
                        return Ext.dd.DropZone.prototype.dropAllowed;
                    } else {
                        return Ext.dd.DropZone.prototype.dropNotAllowed;
                    }

                }.bind(this),

                onNodeDrop: function (target, dd, e, data) {
                    var record = data.records[0],
                        data = record.data,
                        view = this.search.getLayout();

                    if (data.className == coreshop.class_map.coreshop[this.type]) {
                        drag.setDisabled(true);
                        view.setLoading(t('loading'));
                        this.open(data.id, function () {
                            view.setLoading(false);
                            drag.setDisabled(false);
                        });
                        return true;
                    } else {
                        return false;
                    }
                }.bind(this)
            });

        }.bind(this));

        return drag;

    },

    getGrid: function () {

        this.tabbar = new Ext.TabPanel({
            tabPosition: 'top',
            region: 'center',
            deferredRender: true,
            enableTabScroll: true,
            border: false,
            activeTab: 0,
            listeners: {
                afterLayout: function () {
                    this.setActiveTab(0);
                }
            }
        });

        var searchLayout = this.search.getLayout();

        if (searchLayout) {
            searchLayout.on('afterrender', function (layout) {

                layout.setTitle(t('coreshop_' + this.type + '_manage'));
                layout.setIconCls('coreshop_icon_' + this.type);

                searchLayout.onBefore('add', function (item) {
                    var gridQuery = item.query('grid');
                    if (gridQuery.length > 0) {
                        this.setGridPaginator(layout);
                        this.setupContextMenuPlugin();
                        this.enhanceGridLayout(gridQuery[0]);
                    }
                }.bind(this));

            }.bind(this));

            this.tabbar.add(searchLayout);

        }

        return this.tabbar;
    },

    enhanceGridLayout(grid) {

        var toolbar;

        grid.on('beforeedit', function (grid, cell) {
            if (cell.column.hasEditor() === false) {
                return false;
            }
        });

        grid.on('celldblclick', function (view, td, cellIndex, record, tr, rowIndex) {

            if (!view.panel) {
                return;
            }

            var column = view.panel.columns[cellIndex - 1];
            if (column && column.hasEditor() === false) {
                view.setLoading(t('loading'));
                data = grid.getStore().getAt(rowIndex);
                this.open(data.id, function () {
                    view.setLoading(false);
                });
                return false;
            }
        }.bind(this));

        coreshop.broker.fireEvent('sales.list.enhancing.grid', grid);

        toolbar = grid.query('toolbar');
        if (toolbar.length > 0) {
            this.enhanceToolbarLayout(grid, toolbar[0]);
        }

    },

    enhanceToolbarLayout(grid, toolbar) {

        var label = new Ext.Toolbar.TextItem({
            text: t('coreshop_order_list_filter') + ':'
        });

        try {
            var searchAndMove = toolbar.down('[iconCls*=pimcore_icon_search]');
            var justChildrenCheckbox = toolbar.down('[name=onlyDirectChildren]');

            if (searchAndMove) {
                searchAndMove.next().hide();
                searchAndMove.hide();
            }

            if (justChildrenCheckbox) {
                justChildrenCheckbox.next().hide();
                justChildrenCheckbox.hide();
            }
        } catch (ex) {
            // fail silently.
        }

        toolbar.insert(2, [
            label,
            {
                xtype: 'combo',
                value: 'none',
                store: this.getFilterStore(),
                flex: 1,
                valueField: 'id',
                displayField: 'name',
                queryMode: 'local',
                disabled: false,
                name: 'coreshopFilter',
                listeners: {
                    'change': function (field) {
                        grid.getStore().getProxy().setExtraParam('coreshop_filter', field.getValue());
                        this.getGridPaginator().moveFirst();
                    }.bind(this)
                }
            }
        ]);

        coreshop.broker.fireEvent('sales.list.enhancing.toolbar', toolbar, grid);
    },

    open: function (id, callback) {
        coreshop.order.helper.openSale(id, this.type, callback);
    },

    setGridPaginator: function (layout) {
        this.gridPaginator = layout.down('pagingtoolbar');

        coreshop.broker.fireEvent('sales.list.enhancing.grid-paginator', this.gridPaginator);

    },

    getGridPaginator: function () {
        return this.gridPaginator;
    },

    getFilterStore: function () {

        var filterStore = new Ext.data.Store({
            restful: false,
            proxy: new Ext.data.HttpProxy({
                url: '/admin/coreshop/grid/filters/coreshop_' + this.type
            }),
            reader: new Ext.data.JsonReader({}, [
                {name: 'id'},
                {name: 'name'}
            ]),
            listeners: {
                load: function (store) {
                    var rec = {id: 'none', name: t('coreshop_order_list_filter_empty')};
                    store.insert(0, rec);
                }.bind(this)
            }
        });

        filterStore.load();
        return filterStore;
    }
});
