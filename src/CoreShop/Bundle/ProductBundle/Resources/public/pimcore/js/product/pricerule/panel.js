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

pimcore.registerNS('coreshop.product.pricerule.panel');
coreshop.product.pricerule.panel = Class.create(coreshop.rules.panel, {
    /**
     * @var string
     */
    layoutId: 'coreshop_product_price_rule_panel',
    storeId: 'coreshop_product_price_rule',
    iconCls: 'coreshop_icon_price_rule',
    type: 'coreshop_product_pricerules',

    /**
     * @var array
     */
    conditions: [],

    /**
     * @var array
     */
    actions: [],

    /**
     * constructor
     */
    initialize: function () {
        var me = this;

        Ext.Ajax.request({
            url: Routing.generate('coreshop_product_price_rule_getConfig'),
            method: 'GET',
            success: function (result) {
                var config = Ext.decode(result.responseText);
                me.conditions = config.conditions;
                me.actions = config.actions;
            }
        });

        this.routing = {
            add: 'coreshop_product_price_rule_add',
            delete: 'coreshop_product_price_rule_delete',
            get: 'coreshop_product_price_rule_get',
            list: 'coreshop_product_price_rule_list'
        };

        this.panels = [];
        this.store = new Ext.data.Store({
            idProperty: 'id',
            fields: [
                {name: 'id'},
                {name: 'name'}
            ],
            proxy: {
                type: 'ajax',
                url: Routing.generate(this.routing.list),
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }
            }
        });

        this.getLayout();
    },

    getGridConfiguration: function () {
        return {
            store: this.store
        };
    },

    getItemClass: function () {
        return coreshop.product.pricerule.item;
    }
});
