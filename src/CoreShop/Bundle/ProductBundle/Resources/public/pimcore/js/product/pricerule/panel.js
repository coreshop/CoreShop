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
            url: '/admin/coreshop/product_price_rules/get-config',
            method: 'GET',
            success: function (result) {
                var config = Ext.decode(result.responseText);
                me.conditions = config.conditions;
                me.actions = config.actions;
            }
        });

        this.url = {
            add: '/admin/coreshop/product_price_rules/add',
            delete: '/admin/coreshop/product_price_rules/delete',
            get: '/admin/coreshop/product_price_rules/get',
            list: '/admin/coreshop/product_price_rules/list'
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
                url: this.url.list,
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
