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

pimcore.registerNS('pimcore.plugin.coreshop.pricerules.item');

pimcore.plugin.coreshop.pricerules.item = Class.create(pimcore.plugin.coreshop.rules.item, {

    iconCls : 'coreshop_icon_price_rule',

    url : {
        save : '/admin/CoreShop/cart_price_rules/save'
    },

    getPanel: function () {
        this.panel = new Ext.TabPanel({
            activeTab: 0,
            title: this.data.name,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            iconCls : this.iconCls,
            buttons: [{
                text: t('save'),
                iconCls: 'pimcore_icon_apply',
                handler: this.save.bind(this)
            }],
            items: this.getItems()
        });

        this.addVoucherCodes();

        return this.panel;
    },

    getSettings: function () {
        var data = this.data;

        this.settingsForm = Ext.create('Ext.form.Panel', {
            iconCls: 'coreshop_icon_settings',
            title: t('settings'),
            bodyStyle: 'padding:10px;',
            autoScroll: true,
            border:false,
            items: [{
                xtype: 'textfield',
                name: 'name',
                fieldLabel: t('name'),
                width: 250,
                value: data.name
            }, {
                xtype: 'textarea',
                name: 'description',
                fieldLabel: t('description'),
                width: 400,
                height: 100,
                value: data.description
            }, {
                xtype: 'checkbox',
                name: 'active',
                fieldLabel: t('active'),
                checked: this.data.active
            }, {
                xtype: 'checkbox',
                name: 'highlight',
                fieldLabel: t('highlight'),
                checked: this.data.highlight
            }]
        });

        return this.settingsForm;
    },

    addVoucherCodes : function () {
        this.panel.add(this.getVoucherCodes());
    },

    destroyVoucherCodes : function () {
        if (this.voucherCodesPanel) {
            this.getVoucherCodes().destroy();
            this.voucherCodesPanel = null;
        }
    },

    getVoucherCodes : function () {
        if (!this.voucherCodesPanel) {
            var store = new Ext.data.JsonStore({
                remoteSort: true,
                remoteFilter: true,
                autoDestroy: true,
                autoSync: true,
                pageSize: pimcore.helpers.grid.getDefaultPageSize(),
                proxy: {
                    type: 'ajax',
                    url: '/admin/CoreShop/cart_price_rules/get-voucher-codes',
                    reader: {
                        type: 'json',
                        rootProperty: 'data',
                        totalProperty : 'total'
                    },
                    extraParams : {
                        id : this.data.id
                    }
                },
                fields: [
                    { name:'id', type:'int' },
                    { name:'used', type:'boolean' },
                    { name:'uses', type:'int' },
                    { name:'code', type:'string' }
                ]
            });

            var grid = new Ext.grid.Panel({
                store : store,
                plugins: {
                    ptype : 'pimcore.gridfilters',
                    pluginId : 'filter',
                    encode: true,
                    local: false
                },
                columns: [
                    {
                        text: t('code'),
                        dataIndex : 'code',
                        flex : 1
                    },
                    {
                        xtype: 'booleancolumn',
                        text: t('coreshop_cart_pricerule_used'),
                        dataIndex : 'used',
                        flex : 1,
                        trueText: t('yes'),
                        falseText: t('no')
                    },
                    {
                        text: t('coreshop_cart_pricerule_uses'),
                        dataIndex : 'uses',
                        flex : 1
                    }
                ],
                region : 'center',
                flex : 1,
                bbar: pimcore.helpers.grid.buildDefaultPagingToolbar(store)
            });

            grid.on('beforerender', function () {
                this.getStore().load();
            });

            this.voucherCodesPanel = new Ext.panel.Panel({
                iconCls: 'coreshop_price_rule_vouchers',
                title: t('coreshop_cart_pricerule_voucherCodes'),
                autoScroll: true,
                forceLayout: true,
                style : 'padding: 10px',
                layout : 'border',
                items : [
                    grid
                ],
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [
                        {
                            xtype: 'button',
                            text: t('coreshop_cart_pricerule_generate_vouchers'),
                            handler : function () {
                                this.openVoucherGenerationDialog();
                            }.bind(this)
                        },
                        {
                            xtype: 'button',
                            text: t('coreshop_cart_pricerule_vouchers_export'),
                            handler : function () {
                                pimcore.helpers.download('/admin/CoreShop/cart_price_rules/export-voucher-codes?id=' + this.data.id);
                            }.bind(this)
                        }
                    ]
                }]
            });

        }

        return this.voucherCodesPanel;
    },

    openVoucherGenerationDialog : function () {
        var window = new Ext.Window({
            width: 330,
            height: 420,
            modal: true,
            iconCls: 'coreshop_price_rule_vouchers',
            title: t('coreshop_cart_pricerule_generate_vouchers'),
            layout: 'fit',
            items: [{
                xtype : 'form',
                region: 'center',
                bodyPadding: 20,
                items: [
                    {
                        xtype : 'numberfield',
                        name : 'amount',
                        fieldLabel : t('coreshop_cart_pricerule_amount')
                    },
                    {
                        xtype : 'numberfield',
                        name : 'length',
                        fieldLabel : t('coreshop_cart_pricerule_length')
                    },
                    {
                        xtype: 'combo',
                        store: [['alphanumeric', t('coreshop_cart_pricerule_alphanumeric')], ['alphabetic', t('coreshop_cart_pricerule_alphabetic')], ['numeric', t('coreshop_cart_pricerule_numeric')]],
                        triggerAction: 'all',
                        typeAhead: false,
                        editable: false,
                        forceSelection: true,
                        queryMode: 'local',
                        fieldLabel: t('coreshop_cart_pricerule_format'),
                        name: 'format',
                        value : 'alphanumeric'
                    },
                    {
                        xtype : 'textfield',
                        name : 'prefix',
                        fieldLabel : t('coreshop_cart_pricerule_prefix')
                    },
                    {
                        xtype : 'textfield',
                        name : 'suffix',
                        fieldLabel : t('coreshop_cart_pricerule_suffix')
                    },
                    {
                        xtype : 'numberfield',
                        name : 'hyphensOn',
                        fieldLabel : t('coreshop_cart_pricerule_hyphensOn')
                    }
                ],
                buttons: [{
                    text: t('create'),
                    iconCls: 'pimcore_icon_apply',
                    handler: function (btn) {
                        var params = btn.up('form').getForm().getFieldValues();

                        params['id'] = this.data.id;

                        Ext.Ajax.request({
                            url: '/admin/CoreShop/cart_price_rules/generate-voucher-codes',
                            method: 'post',
                            params : params,
                            success: function (response) {
                                var res = Ext.decode(response.responseText);

                                if (res.success) {
                                    pimcore.helpers.showNotification(t('success'), t('success'), 'success');

                                    window.close();
                                    this.getVoucherCodes().down('grid').getStore().load();
                                } else {
                                    pimcore.helpers.showNotification(t('error'), 'error', 'error');
                                }
                            }.bind(this)
                        });
                    }.bind(this)
                }]
            }]
        });

        window.show();
    }
});
