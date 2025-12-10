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

pimcore.registerNS('coreshop.country.panel');
coreshop.country.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_countries_panel',
    storeId: 'coreshop_countries',
    iconCls: 'coreshop_icon_country',
    type: 'coreshop_countries',

    routing: {
        add: 'coreshop_country_add',
        delete: 'coreshop_country_delete',
        get: 'coreshop_country_get',
        list: 'coreshop_country_list'
    },

    initialize: function ($super) {
        this.store = new Ext.data.Store({
            restful: false,
            proxy: new Ext.data.HttpProxy({
                url: Routing.generate(this.routing.list)
            }),
            reader: new Ext.data.JsonReader({
                rootProperty: 'data'
            }, [
                {name: 'id'},
                {name: 'name'},
                {name: 'zoneName'}
            ]),
            autoload: true,
            groupField: 'zoneName',
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
        return coreshop.country.item;
    }
});
