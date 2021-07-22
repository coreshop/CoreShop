/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
