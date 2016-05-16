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

pimcore.registerNS('pimcore.plugin.coreshop.product.specificprice');
pimcore.plugin.coreshop.product.specificprice = Class.create(pimcore.plugin.coreshop.pricerules.panel, {
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
            url: '/plugin/CoreShop/admin_product/get-specific-price-config',
            method: 'GET',
            success: function (result) {
                var config = Ext.decode(result.responseText);
                me.conditions = config.conditions;
                me.actions = config.actions;
            }
        });

        this.url = {
            add : '/plugin/CoreShop/admin_product/add-specific-price?product=' + element.id,
            delete : '/plugin/CoreShop/admin_product/delete-specific-price',
            get : '/plugin/CoreShop/admin_product/get-specific-price',
            list : '/plugin/CoreShop/admin_product/list?product=' + element.id
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
                    url: '/plugin/CoreShop/admin_product/list-specific-prices',
                    reader: {
                        type: 'json',
                        rootProperty : 'data'
                    },
                    extraParams : {
                        product : this.element.id
                    }
                }
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
