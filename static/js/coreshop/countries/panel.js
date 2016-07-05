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
        add : '/plugin/CoreShop/admin_country/add',
        delete : '/plugin/CoreShop/admin_country/delete',
        get : '/plugin/CoreShop/admin_country/get',
        list : '/plugin/CoreShop/admin_country/list'
    },

    getNavigation: function () {
        if (!this.grid) {
            this.store = new Ext.data.Store({
                restful:    false,
                proxy:      new Ext.data.HttpProxy({
                    url : '/plugin/CoreShop/admin_country/list'
                }),
                reader:     new Ext.data.JsonReader({}, [
                    { name:'id' },
                    { name:'name' },
                    { name:'zone' }
                ]),
                autoload:   true,
                groupField: 'zone',
                groupDir: 'ASC'
            });

            this.grid = Ext.create('Ext.grid.Panel', {
                region: 'west',
                store: this.store,
                columns: [
                    {
                        text: '',
                        dataIndex: 'text',
                        flex : 1,
                        renderer: function (value, metadata, record)
                        {
                            metadata.tdCls = record.get('iconCls') + ' td-icon';

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
                groupField: 'zone',
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
                hideHeaders: true
            });

            this.grid.on('beforerender', function () {
                this.getStore().load();
            });

        }

        return this.grid;
    }
});
