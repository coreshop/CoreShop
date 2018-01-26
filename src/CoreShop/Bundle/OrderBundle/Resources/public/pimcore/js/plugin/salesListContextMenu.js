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

        var extraParams = grid.getStore().getProxy().getExtraParams();

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
                    if(data && data.data) {
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

    }
});