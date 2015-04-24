/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

pimcore.registerNS("pimcore.plugin.coreshop.currency.list");
pimcore.plugin.coreshop.currency.list = Class.create({

    initialize: function () {

        this.getTabPanel();
    },

    activate: function () {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.activate("coreshop_currency_list");
    },

    getTabPanel: function () {

        if (!this.panel) {
            this.panel = new Ext.Panel({
                id: "coreshop_currency_list",
                title: t("coreshop_currency"),
                border: false,
                iconCls: "coreshop_icon_currency",
                layout: "fit",
                closable:true,
                items: [this.getGrid()]
            });

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.activate("coreshop_currency_list");


            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("coreshop_currencies");
            }.bind(this));

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    getGrid: function () {

        var proxy = new Ext.data.HttpProxy({
            url: '/plugin/CoreShop/admin_currency/list'
        });
        var reader = new Ext.data.JsonReader({
            totalProperty: 'total',
            successProperty: 'success',
            root: 'data',
            idProperty: "id"
        }, [
            {name: 'id'},
            {name: 'name'},
            {name: 'isoCode'},
            {name: 'numericIsoCode'},
            {name: 'symbol'},
            {name: 'exchangeRate'}
        ]);
        var writer = new Ext.data.JsonWriter();

        var itemsPerPage = 20;

        this.store = new Ext.data.Store({
            id: 'coreshop_currencies_store',
            restful: false,
            proxy: proxy,
            reader: reader,
            writer: writer,
            remoteSort: true,
            baseParams: {
                limit: itemsPerPage
            }
        });
        this.store.load();

        this.pagingtoolbar = new Ext.PagingToolbar({
            pageSize: itemsPerPage,
            store: this.store,
            displayInfo: true,
            displayMsg: '{0} - {1} / {2}',
            emptyMsg: t("no_objects_found")
        });

        // add per-page selection
        this.pagingtoolbar.add("-");

        this.pagingtoolbar.add(new Ext.Toolbar.TextItem({
            text: t("items_per_page")
        }));
        this.pagingtoolbar.add(new Ext.form.ComboBox({
            store: [
                [10, "10"],
                [20, "20"],
                [40, "40"],
                [60, "60"],
                [80, "80"],
                [100, "100"]
            ],
            mode: "local",
            width: 50,
            value: 20,
            triggerAction: "all",
            listeners: {
                select: function (box, rec, index) {
                    this.pagingtoolbar.pageSize = intval(rec.data.field1);
                    this.pagingtoolbar.moveFirst();
                }.bind(this)
            }
        }));

        var typesColumns = [
            {header: t("coreshop_name"), width: 50, sortable: true, dataIndex: 'name'},
            {header: t("coreshop_isoCode"), width: 100, sortable: true, dataIndex: 'isoCode'},
            {header: t("coreshop_numericIsoCode"), width: 60, sortable: true, dataIndex: 'numericIsoCode'},
            {header: t("coreshop_symbol"), width:80,sortable: true, dataIndex: 'symbol'},
            {header: t("coreshop_exchangeRate"), width: 140, sortable: true, dataIndex: 'exchangeRate'},
            {
                xtype: 'actioncolumn',
                width: 30,
                items: [{
                    tooltip: t('delete'),
                    icon: "/pimcore/static/img/icon/cross.png",
                    handler: function (grid, rowIndex) {
                        grid.getStore().removeAt(rowIndex);
                    }.bind(this)
                }]
            }
        ];

        this.grid = new Ext.grid.GridPanel({
            frame: false,
            autoScroll: true,
            store: this.store,
            columnLines: true,
            bbar: this.pagingtoolbar,
            stripeRows: true,
            plugins: [],
            sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
            columns : typesColumns,
            tbar: [
                {
                    text: t('delete'),
                    handler: this.onDelete.bind(this),
                    iconCls: "pimcore_icon_delete",
                    id: "coreshop_currency_button_delete",
                    disabled: true
                }
            ],
            listeners: {
                "rowclick": function () {
                    var rec = this.grid.getSelectionModel().getSelected();
                    if (!rec) {
                        Ext.getCmp("coreshop_currency_button_delete").disable();
                    } else {
                        Ext.getCmp("coreshop_currency_button_delete").enable();
                    }
                }.bind(this)
            }
        });

        return this.grid;
    },

    onDelete: function () {
        var rec = this.grid.getSelectionModel().getSelected();
        if (!rec) {
            return false;
        }
        this.grid.store.remove(rec);

        Ext.getCmp("coreshop_currency_button_delete").disable();
    }
});
