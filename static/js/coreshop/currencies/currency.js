/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */


pimcore.registerNS("pimcore.plugin.coreshop.currencies.currency");
pimcore.plugin.coreshop.currencies.currency = Class.create({

    initialize: function (parentPanel, id) {
        this.parentPanel = parentPanel;
        this.id = id;
        this.data = undefined;

        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_currency/get-currency",
            success: this.loadComplete.bind(this),
            params: {
                id: this.id
            }
        });
    },

    loadComplete: function (transport) {
        var response = Ext.decode(transport.responseText);

        if(response && response.success)
        {
            this.data = response;
            this.initPanel();
        }
    },

    initPanel: function () {

        this.panel = new Ext.Panel({
            title: this.data.currency.name,
            closable: true,
            iconCls: "coreshop_icon_currency",
            layout: "border",
            items : [this.getFormPanel()]
        });

        this.panel.on("beforedestroy", function () {
            delete this.parentPanel.panels["coreshop_currency_" + this.id];
        }.bind(this));

        this.parentPanel.getEditPanel().add(this.panel);
        this.parentPanel.getEditPanel().activate(this.panel);
    },

    getFormPanel : function() {
        this.formPanel = new Ext.FormPanel({
            bodyStyle:'padding:20px 5px 20px 5px;',
            border: false,
            region : "center",
            autoScroll: true,
            forceLayout: true,
            defaults: {
                forceLayout: true
            },
            layout: "pimcoreform",
            buttons: [
                {
                    text: t("save"),
                    handler: this.save.bind(this),
                    iconCls: "pimcore_icon_apply"
                }
            ],
            items: [
                {
                    xtype:'fieldset',
                    autoHeight:true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 300},
                    items :[
                        {
                            fieldLabel: t("coreshop_currency_name"),
                            name: "name",
                            value: this.data.currency.name
                        },
                        {
                            fieldLabel: t("coreshop_currency_isoCode"),
                            name: "isoCode",
                            value: this.data.currency.isoCode
                        },
                        {
                            fieldLabel: t("coreshop_currency_numericIsoCode"),
                            name: "numericIsoCode",
                            value: this.data.currency.numericIsoCode
                        },
                        {
                            fieldLabel: t("coreshop_currency_symbol"),
                            name: "symbol",
                            value: this.data.currency.symbol
                        }
                        ,
                        {
                            fieldLabel: t("coreshop_currency_exchangeRate"),
                            name: "exchangeRate",
                            value: this.data.currency.exchangeRate,
                            xtype : 'spinnerfield'
                        }
                    ]
                }
            ]
        });

        return this.formPanel;
    },

    save: function () {
        var values = this.formPanel.getForm().getFieldValues();

        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_currency/save",
            method: "post",
            params: {
                data: Ext.encode(values),
                id : this.data.currency.id
            },
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);
                    if (res.success) {
                        pimcore.helpers.showNotification(t("success"), t("coreshop_currency_save_success"), "success");
                    } else {
                        pimcore.helpers.showNotification(t("error"), t("coreshop_currency_save_error"),
                            "error", t(res.message));
                    }
                } catch(e) {
                    pimcore.helpers.showNotification(t("error"), t("coreshop_currency_save_error"), "error");
                }
            }
        });
    }
});