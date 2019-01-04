/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.pricerules.item');
coreshop.cart.pricerules.item = Class.create(coreshop.rules.item, {

    iconCls: 'coreshop_icon_price_rule',

    url: {
        save: '/admin/coreshop/cart_price_rules/save'
    },

    getPanel: function () {
        this.panel = new Ext.TabPanel({
            activeTab: 0,
            title: this.data.name,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            iconCls: this.iconCls,
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

    getActionContainerClass: function () {
        return coreshop.cart.pricerules.action;
    },

    getConditionContainerClass: function () {
        return coreshop.cart.pricerules.condition;
    },

    getSettings: function () {
        var data = this.data;

        this.settingsForm = Ext.create('Ext.form.Panel', {
            iconCls: 'coreshop_icon_settings',
            title: t('settings'),
            bodyStyle: 'padding:10px;',
            autoScroll: true,
            border: false,
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
                name: 'isVoucherRule',
                fieldLabel: t('coreshop_is_voucher_rule'),
                checked: this.data.isVoucherRule,
                listeners: {
                    change: function(checkbox, newValue) {
                        if (newValue) {
                            this.getVoucherCodes().enable();
                        }
                        else {
                            this.getVoucherCodes().disable();
                        }
                    }.bind(this)
                }
            }]
        });

        return this.settingsForm;
    },

    addVoucherCodes: function () {
        this.panel.add(this.getVoucherCodes());
    },

    destroyVoucherCodes: function () {
        if (this.voucherCodesPanel) {
            this.getVoucherCodes().destroy();
            this.voucherCodesPanel = null;
        }
    },

    getVoucherCodes: function () {
        if (!this.voucherCodesPanel) {
            var store = new Ext.data.JsonStore({
                remoteSort: true,
                remoteFilter: true,
                autoDestroy: true,
                autoSync: true,
                pageSize: pimcore.helpers.grid.getDefaultPageSize(),
                proxy: {
                    type: 'ajax',
                    reader: {
                        type: 'json',
                        rootProperty: 'data',
                        totalProperty: 'total'
                    },
                    api: {
                        read: '/admin/coreshop/cart_price_rules/get-voucher-codes',
                        destroy: '/admin/coreshop/cart_price_rules/delete-voucher-code'
                    },
                    extraParams: {
                        cartPriceRule: this.data.id
                    }
                },
                fields: [
                    {name: 'id', type: 'int'},
                    {name: 'used', type: 'boolean'},
                    {name: 'uses', type: 'int'},
                    {name: 'code', type: 'string'}
                ]
            });

            var grid = new Ext.grid.Panel({
                store: store,
                plugins: {
                    ptype: 'pimcore.gridfilters',
                    pluginId: 'filter',
                    encode: true,
                    local: false
                },
                columns: [
                    {
                        text: t('coreshop_cart_pricerule_voucher_code'),
                        dataIndex: 'code',
                        flex: 1
                    },
                    {
                        text: t('ccoreshop_cart_pricerule_creation_date'),
                        dataIndex: 'creationDate',
                        flex: 1,
                        renderer: Ext.util.Format.dateRenderer('d.m.Y H:i')
                    },
                    {
                        xtype: 'booleancolumn',
                        text: t('coreshop_cart_pricerule_used'),
                        dataIndex: 'used',
                        flex: 1,
                        trueText: t('yes'),
                        falseText: t('no')
                    },
                    {
                        text: t('coreshop_cart_pricerule_uses'),
                        dataIndex: 'uses',
                        flex: 1
                    },
                    {
                        xtype: 'actioncolumn',
                        width: 40,
                        items: [{
                            isDisabled: function (grid, rowIndex, colIndex, items, record) {
                                return record.data.used === true;
                            },
                            tooltip: t('remove'),
                            iconCls: 'pimcore_icon_deletes',
                            handler: function (grid, rowIndex) {
                                var record = grid.getStore().getAt(rowIndex);
                                grid.getStore().removeAt(rowIndex);
                                console.log(record);
                            }.bind(this)
                        }]
                    }
                ],
                region: 'center',
                flex: 1,
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
                style: 'padding: 10px',
                layout: 'border',
                disabled: !this.data.isVoucherRule,
                items: [
                    grid
                ],
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [
                        {
                            xtype: 'button',
                            text: t('coreshop_cart_pricerule_create_voucher'),
                            handler: function () {
                                this.openVoucherCreateDialog();
                            }.bind(this)
                        },
                        {
                            xtype: 'button',
                            text: t('coreshop_cart_pricerule_generate_vouchers'),
                            handler: function () {
                                this.openVoucherGenerationDialog();
                            }.bind(this)
                        },
                        {
                            xtype: 'button',
                            text: t('coreshop_cart_pricerule_vouchers_export'),
                            handler: function () {
                                pimcore.helpers.download('/admin/coreshop/cart_price_rules/export-voucher-codes?cartPriceRule=' + this.data.id);
                            }.bind(this)
                        }
                    ]
                }]
            });

        }

        return this.voucherCodesPanel;
    },

    openVoucherCreateDialog: function () {
        var window = new Ext.Window({
            width: 330,
            height: 170,
            modal: true,
            iconCls: 'coreshop_price_rule_vouchers',
            title: t('coreshop_cart_pricerule_create_voucher'),
            layout: 'fit',
            items: [{
                xtype: 'form',
                region: 'center',
                bodyPadding: 20,
                items: [
                    {
                        xtype: 'textfield',
                        name: 'code',
                        allowBlank: false,
                        fieldLabel: t('coreshop_cart_pricerule_voucher_code')
                    }
                ],
                buttons: [{
                    text: t('create'),
                    iconCls: 'pimcore_icon_apply',
                    handler: function (btn) {
                        btn.setDisabled(true);
                        var params = btn.up('form').getForm().getFieldValues();

                        params['cartPriceRule'] = this.data.id;

                        Ext.Ajax.request({
                            url: '/admin/coreshop/cart_price_rules/create-voucher-code',
                            method: 'post',
                            jsonData: params,
                            success: function (response) {
                                var res = Ext.decode(response.responseText);
                                if (res.success) {
                                    pimcore.helpers.showNotification(t('success'), t('success'), 'success');
                                    window.close();
                                    this.getVoucherCodes().down('grid').getStore().load();
                                } else {
                                    btn.setDisabled(false);
                                    pimcore.helpers.showNotification(t('error'), (res.message ? res.message : 'error'), 'error');
                                }
                            }.bind(this),
                            failure: function(response) {
                                btn.setDisabled(false);
                            }.bind(this)
                        });
                    }.bind(this)
                }]
            }]
        });

        window.show();
    },

    openVoucherGenerationDialog: function () {
        var window = new Ext.Window({
            width: 330,
            height: 420,
            modal: true,
            iconCls: 'coreshop_price_rule_vouchers',
            title: t('coreshop_cart_pricerule_generate_vouchers'),
            layout: 'fit',
            items: [{
                xtype: 'form',
                region: 'center',
                bodyPadding: 20,
                items: [
                    {
                        xtype: 'numberfield',
                        name: 'amount',
                        fieldLabel: t('coreshop_cart_pricerule_amount')
                    },
                    {
                        xtype: 'numberfield',
                        name: 'length',
                        fieldLabel: t('coreshop_cart_pricerule_length')
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
                        value: 'alphanumeric'
                    },
                    {
                        xtype: 'textfield',
                        name: 'prefix',
                        fieldLabel: t('coreshop_cart_pricerule_prefix')
                    },
                    {
                        xtype: 'textfield',
                        name: 'suffix',
                        fieldLabel: t('coreshop_cart_pricerule_suffix')
                    },
                    {
                        xtype: 'numberfield',
                        name: 'hyphensOn',
                        fieldLabel: t('coreshop_cart_pricerule_hyphensOn')
                    }
                ],
                buttons: [{
                    text: t('create'),
                    iconCls: 'pimcore_icon_apply',
                    handler: function (btn) {
                        btn.setDisabled(true);
                        var params = btn.up('form').getForm().getFieldValues();

                        params['cartPriceRule'] = this.data.id;

                        Ext.Ajax.request({
                            url: '/admin/coreshop/cart_price_rules/generate-voucher-codes',
                            method: 'post',
                            jsonData: params,
                            success: function (response) {
                                var res = Ext.decode(response.responseText);
                                if (res.success) {
                                    pimcore.helpers.showNotification(t('success'), t('success'), 'success');
                                    window.close();
                                    this.getVoucherCodes().down('grid').getStore().load();
                                } else {
                                    btn.setDisabled(false);
                                    pimcore.helpers.showNotification(t('error'), 'error', 'error');
                                }
                            }.bind(this),
                            failure: function(response) {
                                btn.setDisabled(false);
                            }.bind(this)
                        });
                    }.bind(this)
                }]
            }]
        });

        window.show();
    }
});
