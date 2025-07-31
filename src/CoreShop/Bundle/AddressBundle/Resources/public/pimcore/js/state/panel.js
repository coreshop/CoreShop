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

pimcore.registerNS('coreshop.state.panel');
coreshop.state.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_states_panel',
    storeId: 'coreshop_states',
    iconCls: 'coreshop_icon_state',
    type: 'coreshop_states',
    permission: 'coreshop_permission_state',

    routing: {
        add: 'coreshop_state_add',
        delete: 'coreshop_state_delete',
        get: 'coreshop_state_get',
        list: 'coreshop_state_list'
    },

    initialize: function ($super) {
        this.store = new Ext.data.Store({
            restful: false,
            proxy: new Ext.data.HttpProxy({
                url: Routing.generate(this.routing.list)
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
