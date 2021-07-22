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

pimcore.registerNS('coreshop.state.panel');
coreshop.state.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_states_panel',
    storeId: 'coreshop_states',
    iconCls: 'coreshop_icon_state',
    type: 'coreshop_states',

    url: {
        add: '/admin/coreshop/states/add',
        delete: '/admin/coreshop/states/delete',
        get: '/admin/coreshop/states/get',
        list: '/admin/coreshop/states/list'
    },

    initialize: function ($super) {
        this.store = new Ext.data.Store({
            restful: false,
            proxy: new Ext.data.HttpProxy({
                url: this.url.list
            }),
            reader: new Ext.data.JsonReader({}, [
                {name: 'id'},
                {name: 'name'},
                {name: 'countryName'}
            ]),
            autoload: true,
            groupField: 'countryName',
            groupDir: 'ASC'
        });

        $super();
    },

    getGridConfiguration: function () {
        return {
            store: this.store,
            groupField: 'zoneName',
            groupDir: 'ASC',
            features: [{
                ftype: 'grouping',

                // You can customize the group's header.
                groupHeaderTpl: '{name} ({children.length})',
                enableNoGroups: true,
                startCollapsed: true
            }]
        };
    },

    getItemClass: function() {
        return coreshop.state.item;
    }
});
