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
        add : '/plugin/CoreShop/admin_filter/add',
        delete : '/plugin/CoreShop/admin_filter/delete',
        get : '/plugin/CoreShop/admin_filter/get',
        list : '/plugin/CoreShop/admin_filter/list'
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
            url: '/plugin/CoreShop/admin_filter/get-config',
            method: 'GET',
            success: function (result) {
                var config = Ext.decode(result.responseText);

                me.conditions = config.conditions;
                me.similarities = config.similarities;
            }
        });

        // create layout
        this.getLayout();

        this.panels = [];
    }
});
