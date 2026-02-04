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

pimcore.registerNS('coreshop.rules.panel');
coreshop.rules.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var array
     */
    conditions: [],

    /**
     * @var array
     */
    actions: [],

    /**
     * @var object
     */
    config: {},

    /**
     * constructor
     */
    initialize: function () {
        var me = this;

        Ext.Ajax.request({
            url: this.routing.config ? Routing.generate(this.routing.config) : this.url.config,
            method: 'GET',
            success: function (result) {
                var config = Ext.decode(result.responseText);
                me.conditions = config.conditions;
                me.actions = config.actions;

                me.config = config;
            }
        });

        // create layout
        this.getLayout();

        this.panels = [];
    },

    getGridDisplayColumnRenderer: function (value, metadata, record) {
        metadata.tdAttr = 'data-qtip="ID: ' + record.get('id') + '"';
        if(record.get('active') === false) {
            metadata.tdCls = 'pimcore_rule_disabled';
        }
        return value;
    },

    getItemClass: function () {
        return coreshop.rules.item;
    },

    getActions: function () {
        return this.actions;
    },

    getConfig: function () {
        return this.config;
    },

    getConditions: function () {
        return this.conditions;
    }
});
