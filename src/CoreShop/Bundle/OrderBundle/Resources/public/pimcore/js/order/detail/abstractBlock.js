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
    }
});
