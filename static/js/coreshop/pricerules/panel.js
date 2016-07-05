/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.pricerules.panel');

pimcore.plugin.coreshop.pricerules.panel = Class.create(pimcore.plugin.coreshop.abstract.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_price_rules_panel',
    storeId : 'coreshop_pricerules',
    iconCls : 'coreshop_icon_price_rule',
    type : 'cart_pricerules',

    url : {
        add : '/plugin/CoreShop/admin_price-rule/add',
        delete : '/plugin/CoreShop/admin_price-rule/delete',
        get : '/plugin/CoreShop/admin_price-rule/get',
        list : '/plugin/CoreShop/admin_price-rule/list'
    },

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
            url: '/plugin/CoreShop/admin_price-rule/get-config',
            method: 'GET',
            success: function (result) {
                var config = Ext.decode(result.responseText);
                me.conditions = config.conditions;
                me.actions = config.actions;
            }
        });

        // create layout
        this.getLayout();

        this.panels = [];
    },

    getItemClass : function () {
        return pimcore.plugin.coreshop.pricerules.item;
    }
});
