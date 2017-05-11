/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
*/

pimcore.registerNS('pimcore.plugin.coreshop.product.pricerule.panel');
pimcore.plugin.coreshop.product.pricerule.panel = Class.create(pimcore.plugin.coreshop.pricerules.panel, {
    /**
     * @var string
     */
    layoutId: 'coreshop_product_price_rule_panel',
    storeId : 'coreshop_product_price_rule',
    iconCls : 'coreshop_icon_price_rule',
    type : 'product_pricerules',

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
    initialize: function () {
        var me = this;

        Ext.Ajax.request({
            url: '/admin/CoreShop/product_price_rules/get-config',
            method: 'GET',
            success: function (result) {
                var config = Ext.decode(result.responseText);
                me.conditions = config.conditions;
                me.actions = config.actions;
            }
        });

        this.url = {
            add : '/admin/CoreShop/product_price_rules/add',
            delete : '/admin/CoreShop/product_price_rules/delete',
            get : '/admin/CoreShop/product_price_rules/get',
            list : '/admin/CoreShop/product_price_rules/list'
        };

        this.panels = [];

        this.getLayout();
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
                    url: this.url.list,
                    reader: {
                        type: 'json',
                        rootProperty : 'data'
                    }
                }
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

    getItemClass : function () {
        return pimcore.plugin.coreshop.product.pricerule.item;
    }
});
