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

pimcore.registerNS('coreshop.order.order.create.step');
pimcore.registerNS('coreshop.order.order.create.abstractStep');
coreshop.order.order.create.abstractStep = Class.create({
    eventManager: null,
    creationPanel: null,

    initialize: function (creationPanel, eventManager) {
        var me = this;

        me.creationPanel = creationPanel;
        me.eventManager = eventManager;

        if (Ext.isFunction(me.initStep)) {
            me.initStep();
        }
    },

    isValid: function () {
        return true;
    },

    reset: function() {

    },

    getPriority: function () {
        Ext.Error.raise('implement me');
    },

    getValues: function () {
        Ext.Error.raise('implement me');
    },

    getPreviewValues: function () {
        return this.getValues();
    },

    getName: function() {
        Ext.Error.raise('implement me');
    },

    getPanel: function() {
        Ext.Error.raise('implement me');
    },

    setPreviewData: function(data) {
        Ext.Error.raise('implement me');
    },

    getLayout: function () {
        var tools = Ext.isFunction(this.getTools) ? this.getTools() : [];
        var iconCls = Ext.isFunction(this.getIconCls) ? this.getIconCls() : '';
        var panel = this.getPanel();

        this.panel = panel;
        this.layout = new Ext.panel.Panel({
            margin: '15 0 15 0',
            iconCls: iconCls,
            title: this.getName(),
            items: panel,
            tools: tools
        });

        return this.layout;
    }
});
