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

pimcore.registerNS('pimcore.plugin.coreshop.product.specificprice.panel');
pimcore.plugin.coreshop.product.specificprice.panel = Class.create(pimcore.plugin.coreshop.pricerules.panel, {
    /**
     * @var string
     */
    layoutId: 'coreshop_product_specific_price_panel',
    storeId : 'coreshop_product_specific_price',
    iconCls : 'coreshop_icon_price_rule',
    type : 'productSpecificPrice',

    /**
     * @var array
     */
    conditions: [],

    /**
     * @var array
     */
    actions: [],

    /**
     * constructor
     */
    initialize: function (element) {
        var me = this;

        this.layoutId = this.layoutId + '_' + element.id;

        this.element = element;

        Ext.Ajax.request({
            url: '/plugin/CoreShop/admin_product-specific-price/get-config',
            method: 'GET',
            success: function (result) {
                var config = Ext.decode(result.responseText);
                me.conditions = config.conditions;
                me.actions = config.actions;
            }
        });

        this.url = {
            add : '/plugin/CoreShop/admin_product-specific-price/add?product=' + element.id,
            delete : '/plugin/CoreShop/admin_product-specific-price/delete',
            get : '/plugin/CoreShop/admin_product-specific-price/get',
            list : '/plugin/CoreShop/admin_product-specific-price/list?product=' + element.id
        };

        this.panels = [];
    },

    getLayout: function ()
    {
        if (!this.layout) {
            // create new panel
            this.layout = new Ext.Panel({
                id: this.layoutId,
                title: t('coreshop_' + this.type),
                iconCls: this.iconCls,
                border: false,
                layout: 'border',
                items: this.getItems()
            });
        }

        return this.layout;
    },

    getNavigation: function () {
        if (!this.grid) {

            this.store = new Ext.data.Store({
                idProperty: 'id',
                fields : [
                    { name:'id' },
                    { name:'name' }
                ],
                proxy: {
                    type: 'ajax',
                    url: '/plugin/CoreShop/admin_product-specific-price/list',
                    reader: {
                        type: 'json',
                        rootProperty : 'data'
                    },
                    extraParams : {
                        product : this.element.id
                    }
                },
                reader:     new Ext.data.JsonReader({}, [
                    { name:'id' },
                    { name:'name' },
                    { name:'type' }
                ]),
                autoload:   true,
                groupField: 'type',
                groupDir: 'DESC'
            });

            this.grid = Ext.create('Ext.grid.Panel', {
                region: 'west',
                store: this.store,
                columns: [
                    {
                        text: '',
                        dataIndex: 'text',
                        flex : 1
                    }
                ],
                groupField: 'type',
                groupDir: 'DESC',
                features: [{
                    ftype: 'grouping',

                    // You can customize the group's header.
                    groupHeaderTpl: Ext.create('Ext.XTemplate',
                        '<div>{name:this.translateName} ({children.length})</div>',
                        {
                            translateName: function (name) {
                                return t('coreshop_product_' + name + 's');
                            }
                        }
                    ),
                    enableNoGroups:true,
                    startCollapsed : false
                }],
                listeners : this.getListeners(),
                useArrows: true,
                autoScroll: true,
                animate: true,
                containerScroll: true,
                width: 200,
                split: true,
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
    },

    getListeners: function () {
        var listeners = this.getTreeNodeListeners();

        listeners['itemclick'] = this.openPriceRule.bind(this);

        return listeners;
    },

    getItemClass : function() {
        return pimcore.plugin.coreshop.product.specificprice.item;
    },

    openPriceRule : function(grid, record) {
        if(record.get("type") == "pricerule") {
            Ext.Msg.alert(t('open_target'), t('coreshop_pricerules_open_in_product_rules'));
            return;
        }

        this.openItem(record.data);
    }
});
