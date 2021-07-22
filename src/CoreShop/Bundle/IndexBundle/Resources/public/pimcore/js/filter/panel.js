/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.filter.panel');

coreshop.filter.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_filters_panel',
    storeId: 'coreshop_filters',
    iconCls: 'coreshop_icon_filters',
    type: 'coreshop_filters',

    routing: {
        add: 'coreshop_filter_add',
        delete: 'coreshop_filter_delete',
        get: 'coreshop_filter_get',
        list: 'coreshop_filter_list'
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

        Ext.Ajax.request({
            url: Routing.generate('coreshop_filter_getConfig'),
            method: 'GET',
            success: function (result) {
                var config = Ext.decode(result.responseText);

                me.pre_conditions = config.pre_conditions;
                me.user_conditions = config.user_conditions;
                //me.similarities = config.similarities;
            }
        });

        // create layout
        this.getLayout();

        this.panels = [];
    },

    getItemClass: function() {
        return coreshop.filter.item;
    }
});
