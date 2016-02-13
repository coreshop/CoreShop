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

pimcore.registerNS("pimcore.plugin.coreshop.filters.panel");

pimcore.plugin.coreshop.filters.panel = Class.create(pimcore.plugin.coreshop.abstract.panel, {

    /**
     * @var string
     */
    layoutId: "coreshop_product_filters_panel",
    storeId : "coreshop_product_filters",
    iconCls : "coreshop_icon_product_filters",
    type : "filters",

    url : {
        add : "/plugin/CoreShop/admin_Filter/add",
        delete : "/plugin/CoreShop/admin_Filter/delete",
        get : "/plugin/CoreShop/admin_Filter/get",
        list : "/plugin/CoreShop/admin_Filter/list"
    },

    /**
     * @var array
     */
    conditions: [],

    /**
     * constructor
     */
    initialize: function() {
        var me = this;

        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_Filter/get-config",
            method: "GET",
            success: function(result){
                var config = Ext.decode(result.responseText);

                me.conditions = config.conditions;
            }
        });

        // create layout
        this.getLayout();

        this.panels = [];
    }
});
