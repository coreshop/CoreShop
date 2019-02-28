/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.product.unit.panel');
coreshop.product.unit.panel = Class.create(coreshop.resource.panel, {

    layoutId: 'coreshop_product_unit_panel',
    storeId: 'coreshop_product_unit',
    iconCls: 'coreshop_icon_product_units',
    type: 'coreshop_product_units',

    /**
     * constructor
     */
    initialize: function () {
        var me = this;

        Ext.Ajax.request({
            url: '/admin/coreshop/product_units/get-config',
            method: 'GET',
            success: function (result) {
                var config = Ext.decode(result.responseText);
                me.conditions = config.conditions;
                me.actions = config.actions;
            }
        });

        this.url = {
            add: '/admin/coreshop/product_units/add',
            delete: '/admin/coreshop/product_units/delete',
            get: '/admin/coreshop/product_units/get',
            list: '/admin/coreshop/product_units/list'
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

    prepareAdd: function (object) {
        object['defaultPrecision'] = 0;
        return object;
    },

    getGridConfiguration: function () {
        return {
            store: this.store
        };
    },

    getItemClass: function () {
        return coreshop.product.unit.object.item;
    }
});
