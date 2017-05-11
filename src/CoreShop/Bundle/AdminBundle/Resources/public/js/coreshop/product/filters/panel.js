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

pimcore.registerNS('pimcore.plugin.coreshop.filters.panel');

pimcore.plugin.coreshop.filters.panel = Class.create(pimcore.plugin.coreshop.abstract.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_product_filters_panel',
    storeId : 'coreshop_product_filters',
    iconCls : 'coreshop_icon_product_filters',
    type : 'filters',

    url : {
        add : '/admin/CoreShop/filters/add',
        delete : '/admin/CoreShop/filters/delete',
        get : '/admin/CoreShop/filters/get',
        list : '/admin/CoreShop/filters/list'
    },

    /**
     * @var array
     */
    conditions: [],

    /**
     * constructor
     */
    initialize: function () {
        var me = this;

        pimcore.globalmanager.get('coreshop_indexes').load();

        Ext.Ajax.request({
            url: '/admin/CoreShop/filters/get-config',
            method: 'GET',
            success: function (result) {
                var config = Ext.decode(result.responseText);

                me.conditions = config.conditions;
                //me.similarities = config.similarities;
            }
        });

        // create layout
        this.getLayout();

        this.panels = [];
    }
});
