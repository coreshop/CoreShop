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

pimcore.registerNS('pimcore.plugin.coreshop.states.panel');
pimcore.plugin.coreshop.states.panel = Class.create(pimcore.plugin.coreshop.abstract.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_states_panel',
    storeId : 'coreshop_states',
    iconCls : 'coreshop_icon_state',
    type : 'states',

    url : {
        add : '/plugin/CoreShop/admin_state/add',
        delete : '/plugin/CoreShop/admin_state/delete',
        get : '/plugin/CoreShop/admin_state/get',
        list : '/plugin/CoreShop/admin_state/list'
    },

    getNavigation: function () {
        if (!this.grid) {
            this.store = new Ext.data.Store({
                restful:    false,
                proxy:      new Ext.data.HttpProxy({
                    url : '/plugin/CoreShop/admin_state/list'
                }),
                reader:     new Ext.data.JsonReader({}, [
                    { name:'id' },
                    { name:'name' },
                    { name:'country' }
                ]),
                autoload:   true,
                groupField: 'country',
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
                groupField: 'country',
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
                            text: t('coreshop_' + this.type + '_add'),
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
