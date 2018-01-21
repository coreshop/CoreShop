/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.sale.list');
coreshop.order.sale.list = Class.create({

    type: '',
    search: null,
    grid: null,
    gridConfig: {},
    store: null,
    contextMenuPlugin: null,
    columns: [],
    storeFields: [],

    initialize: function () {
        Ext.Ajax.request({
            url: '/admin/coreshop/' + this.type + '/get-folder-configuration',
            ignoreErrors: true,
            success: function (response) {
                var data = Ext.decode(response.responseText);
                this.gridConfig = data;
                this.setClassFolder()
            }.bind(this)
        });

        this.setupContextMenuPlugin();
    },

    setupContextMenuPlugin: function () {
        this.contextMenuPlugin = new coreshop.sales.plugin.salesListContextMenu(
            function (id) {
                this.open(id);
            }.bind(this),
            []
        );
    },

    setClassFolder: function () {
        Ext.Ajax.request({
            url: '/admin/object/get-folder',
            params: {id: this.gridConfig.folderId},
            ignoreErrors: true,
            success: function (response) {
                var data = Ext.decode(response.responseText),
                    folderClass = [];

                Ext.Array.each(data.classes, function (objectClass) {
                    if (objectClass.name === this.gridConfig.className) {
                        folderClass.push(objectClass);
                    }
                }.bind(this));

                data.classes = folderClass;
                this.search = new pimcore.object.search({data: data, id: this.gridConfig.folderId}, 'folder');
                this.getLayout()
            }.bind(this)
        });
    },

    activate: function () {
        var tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.setActiveItem('coreshop_' + this.type);
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
                        }
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

    getGrid: function () {

        this.tabbar = new Ext.TabPanel({
            tabPosition: "top",
            region: 'center',
            deferredRender: true,
            enableTabScroll: true,
            border: false,
            activeTab: 0
        });

        var searchLayout = this.search.getLayout();
        //searchLayout.checkboxOnlyDirectChildren.hide();

        this.tabbar.add(searchLayout);
        return this.tabbar;
    },

    open: function (id, callback) {
        coreshop.order.helper.openSale(id, this.type, callback);
    }
});