/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS("pimcore.plugin.coreshop.pricerules.panel");

pimcore.plugin.coreshop.pricerules.panel = Class.create(pimcore.plugin.coreshop.abstract.panel, {

    /**
     * @var string
     */
    layoutId: "coreshop_price_rules_panel",
    storeId : "coreshop_price_rules",
    iconCls : "coreshop_icon_price_rule",
    type : "pricerules",

    url : {
        add : "/plugin/CoreShop/admin_PriceRules/add",
        delete : "/plugin/CoreShop/admin_PriceRules/delete",
        get : "/plugin/CoreShop/admin_PriceRules/get",
        list : "/plugin/CoreShop/admin_PriceRules/list"
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
    initialize: function() {
        var me = this;

        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_PriceRules/get-config",
            method: "GET",
            success: function(result){
                var config = Ext.decode(result.responseText);
                me.conditions = config.conditions;
                me.actions = config.actions;
            }
        });

        // create layout
        this.getLayout();

        this.panels = [];
    }
});
