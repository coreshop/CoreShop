/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.countries.panel');
pimcore.plugin.coreshop.countries.panel = Class.create(pimcore.plugin.coreshop.abstract.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_countries_panel',
    storeId : 'coreshop_countries',
    iconCls : 'coreshop_icon_country',
    type : 'countries',

    url : {
        add : '/admin/CoreShop/countries/add',
        delete : '/admin/CoreShop/countries/delete',
        get : '/admin/CoreShop/countries/get',
        list : '/admin/CoreShop/countries/list'
    },

    getNavigation: function () {
        if (!this.grid) {
            this.store = new Ext.data.Store({
                restful:    false,
                proxy:      new Ext.data.HttpProxy({
                    url : this.url.list
                }),
                reader:     new Ext.data.JsonReader({
                    rootProperty: 'data'
                }, [
                    { name:'id' },
                    { name:'name' },
                    { name:'zoneName' }
                ]),
                autoload:   true,
                groupField: 'zoneName',
                groupDir: 'ASC'
            });

            this.grid = Ext.create('Ext.grid.Panel', {
                region: 'west',
                store: this.store,
                columns: [
                    {
                        text: '',
                        dataIndex: 'name',
                        flex : 1,
                        renderer: function (value, metadata, record)
                        {
                            metadata.tdAttr = 'data-qtip="ID: ' + record.get("id") + '"';

                            return value;
                        }
                    }
                ],
                listeners : this.getTreeNodeListeners(),
                useArrows: true,
                autoScroll: true,
                animate: true,
                containerScroll: true,
                width: 200,
                split: true,
                groupField: 'zoneName',
                groupDir: 'ASC',
                features: [{
                    ftype: 'grouping',

                    // You can customize the group's header.
                    groupHeaderTpl: '{name} ({children.length})',
                    enableNoGroups:true,
                    startCollapsed : true
                }],
                tbar: {
                    items: [
                        {
                            // add button
                            text: t('add'),
                            iconCls: 'pimcore_icon_add',
                            handler: this.addItem.bind(this)
                        }
                    ]
                },
                bbar : {
                    items : ['->', {
                        iconCls: 'pimcore_icon_reload',
                        scale : 'small',
                        handler: function() {
                            this.grid.getStore().load();
                        }.bind(this)
                    }]
                },
                hideHeaders: true
            });

            this.grid.on('beforerender', function () {
                this.getStore().load();
            });

        }

        return this.grid;
    }
});
