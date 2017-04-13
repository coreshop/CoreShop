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

pimcore.registerNS('pimcore.plugin.coreshop.payment.provider.panel');
pimcore.plugin.coreshop.payment.provider.panel = Class.create(pimcore.plugin.coreshop.abstract.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_payment_providers_panel',
    storeId : 'coreshop_payment_providers',
    iconCls : 'coreshop_icon_payment_provider',
    type : 'payment_provider',

    url : {
        add : '/admin/CoreShop/payment_providers/add',
        delete : '/admin/CoreShop/payment_providers/delete',
        get : '/admin/CoreShop/payment_providers/get',
        list : '/admin/CoreShop/payment_providers/list',
        config : '/admin/CoreShop/payment_providers/get-config'
    },

    factoryTypes : null,

    /**
     * constructor
     */
    initialize: function () {
        this.getConfig();

        this.panels = [];
    },

    getConfig : function() {
        this.factoryTypes = new Ext.data.ArrayStore({
            data : [],
            expandedData: true
        });

        pimcore.globalmanager.add('coreshop_payment_provider_factories', this.factoryTypes);

        Ext.Ajax.request({
            url: this.url.config,
            method: 'get',
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);

                    this.factoryTypes.loadData(res.factories);

                    this.getLayout();
                } catch (e) {
                    //pimcore.helpers.showNotification(t('error'), t('coreshop_save_error'), 'error');
                }
            }.bind(this)
        });
    },

    getItemClass : function () {
        return pimcore.plugin.coreshop.payment.provider.item;
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
                    { name:'identifier' }
                ]),
                autoload:   true
            });

            this.grid = Ext.create('Ext.grid.Panel', {
                region: 'west',
                store: this.store,
                columns: [
                    {
                        text: '',
                        dataIndex: 'identifier',
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
    },

    prepareAdd : function(object) {

        object['identifier'] = object.name;

        return object;
    }
});
