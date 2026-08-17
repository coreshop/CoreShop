/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.order.order.detail.blocks');
pimcore.registerNS('coreshop.order.order.detail.abstractBlock');
coreshop.order.order.detail.abstractBlock = Class.create({
    eventManager: null,
    panel: null,
    sale: null,

    initialize: function (panel, eventManager) {
        var me = this;

        me.panel = panel;
        me.eventManager = eventManager;

        if (Ext.isFunction(me.initBlock)) {
            me.initBlock();
        }

        me.setSale(panel.sale);
    },

    setSale: function(sale) {
        var me = this;

        me.sale = sale;

        me.updateSale();
    },

    updateSale: function() {

    },

    getPriority: function () {
        Ext.Error.raise('implement me');
    },

    getPanel: function () {
        Ext.Error.raise('implement me');
    },

    getTopBarItems: function() {
        return [];
    },

    getLayout: function () {
        var me = this;

        return me.getPanel();
    },

    getUpdateValues: function() {
        return {};
    }
});
