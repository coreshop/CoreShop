pimcore.registerNS('coreshop.sales.plugin.salesListContextMenu');
coreshop.sales.plugin.salesListContextMenu = Class.create({
    openerCallback: null,
    allowedClasses: [],
    initialize: function (openerCallback, allowedClasses) {
        this.openerCallback = openerCallback;
        this.allowedClasses = allowedClasses;
        pimcore.plugin.broker.registerPlugin(this);
    },
    prepareOnRowContextmenu: function (menu, grid, selectedRows) {

        var extraParams = grid.getStore().getProxy().getExtraParams(),
            _ = this;

        if (!extraParams || !extraParams['class']) {
            return;
        }

        if (!Ext.Array.contains(this.allowedClasses, extraParams['class'])) {
            return;
        }

        menu.removeAll(true);

        if (selectedRows.length <= 1) {
            menu.add(new Ext.menu.Item({
                text: t('open'),
                iconCls: 'pimcore_icon_open',
                handler: function (grid, menu) {
                    var $el = Ext.get(menu.focusAnchor),
                        gridView = grid.getView(),
                        rowIndex = gridView.indexOf($el.el.up('table')),
                        data = grid.getStore().getAt(rowIndex);
                    if (data && data.data) {
                        this.openerCallback(data.data.id);
                    }
                }.bind(this, grid, menu)
            }));
        } else {
            menu.add(new Ext.menu.Item({
                text: t('open_selected'),
                iconCls: 'pimcore_icon_open',
                handler: function () {
                    for (var i = 0; i < selectedRows.length; i++) {
                        this.openerCallback(selectedRows[i].data.id);
                    }
                }.bind(this)
            }));
        }

        this.getBulkStore().on('load', function () {

            var bulkItems = [];
            for (var i = 0; i < this.getCount(); i++) {
                bulkItems.push({
                    text: this.getAt(i).get('name'),
                    iconCls: 'pimcore_icon_table',
                    name: this.getAt(i).get('id'),
                    handler: function (item) {
                        _.applyBulk(grid, item.name, selectedRows)
                    }
                });
            }

            menu.add({
                text: t('coreshop_order_list_bulk'),
                iconCls: 'pimcore_icon_table pimcore_icon_overlay_go',
                hideOnClick: false,
                menu: bulkItems
            });

        }).load();

    },

    getBulkStore: function () {
        return new Ext.data.Store({
            restful: false,
            proxy: new Ext.data.HttpProxy({
                url: '/admin/coreshop/orderlist/get-bulk'
            }),
            reader: new Ext.data.JsonReader({}, [
                {name: 'id'},
                {name: 'name'}
            ])
        });
    },

    applyBulk: function (grid, bulkId, selectedRows) {

        var selectedObjects = [];
        for (var i = 0; i < selectedRows.length; i++) {
            selectedObjects.push(selectedRows[i].id)
        }

        grid.setLoading(t('loading'));

        Ext.Ajax.request({
            url: '/admin/coreshop/orderlist/apply-bulk',
            method: 'post',
            params: {
                bulkId: bulkId,
                ids: Ext.encode(selectedObjects)
            },
            success: function (response) {
                grid.setLoading(false);
                var res = Ext.decode(response.responseText);
                this.showMessageWindow(res.success ? 'success' : 'error', res.message);
            }.bind(this),
            failure: function (response) {
                grid.setLoading(false);
                // do nothing: pimcore will through a error window.
            }.bind(this)
        });

    },

    showMessageWindow: function (type, message) {
        var win = new Ext.Window({
            modal: true,
            iconCls: 'pimcore_icon_' + type,
            title: t('coreshop_order_list_bulk_review'),
            width: 700,
            maxHeight: 500,
            html: message,
            autoScroll: true,
            bodyStyle: 'padding: 10px; background:#fff;',
            buttonAlign: 'center',
            shadow: false,
            closable: false,
            buttons: [{
                text: t('OK'),
                handler: function () {
                    //@todo: reload grid.
                    win.close();
                }
            }]
        });

        win.show();
    }

});