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


pimcore.registerNS("pimcore.plugin.coreshop.countries.country");
pimcore.plugin.coreshop.countries.country= Class.create({

    initialize: function (parentPanel, id) {
        this.parentPanel = parentPanel;
        this.id = id;
        this.data = undefined;

        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_country/get-country",
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

        this.panel = new Ext.panel.Panel({
            title: this.data.country.name,
            closable: true,
            iconCls: "coreshop_icon_country",
            layout: "border",
            items : [this.getFormPanel()]
        });

        this.panel.on("beforedestroy", function () {
            delete this.parentPanel.panels["coreshop_country_" + this.id];
        }.bind(this));

        this.parentPanel.getEditPanel().add(this.panel);
        this.parentPanel.getEditPanel().setActiveItem(this.panel);
    },

    activate : function() {
        this.parentPanel.getEditPanel().setActiveItem(this.panel);
    },

    getFormPanel : function() {

        /*
        }*/

        this.formPanel = new Ext.form.Panel({
            bodyStyle:'padding:20px 5px 20px 5px;',
            border: false,
            region : "center",
            autoScroll: true,
            forceLayout: true,
            defaults: {
                forceLayout: true
            },
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
                            fieldLabel: t("coreshop_country_name"),
                            name: "name",
                            value: this.data.country.name
                        },
                        {
                            fieldLabel: t("coreshop_country_isoCode"),
                            name: "isoCode",
                            value: this.data.country.isoCode
                        },
                        {
                            xtype : 'checkbox',
                            fieldLabel: t("coreshop_country_active"),
                            name: "active",
                            checked: this.data.country.active === 1
                        },
                        {
                            xtype:'combo',
                            fieldLabel:t('coreshop_country_currency'),
                            typeAhead:true,
                            value:this.data.country.currencyId,
                            mode:'local',
                            listWidth:100,
                            store:pimcore.globalmanager.get("coreshop_currencies"),
                            displayField:'name',
                            valueField:'id',
                            forceSelection:true,
                            triggerAction:'all',
                            name:'currencyId',
                            listeners: {
                                change: function () {
                                    this.forceReloadOnSave = true;
                                }.bind(this),
                                select: function () {
                                    this.forceReloadOnSave = true;
                                }.bind(this)
                            }
                        },
                        {
                            xtype:'combo',
                            fieldLabel:t('coreshop_country_zone'),
                            typeAhead:true,
                            value:this.data.country.zoneId,
                            mode:'local',
                            listWidth:100,
                            store:pimcore.globalmanager.get("coreshop_zones"),
                            displayField:'name',
                            valueField:'id',
                            forceSelection:true,
                            triggerAction:'all',
                            name:'zoneId',
                            listeners: {
                                change: function () {
                                    this.forceReloadOnSave = true;
                                }.bind(this),
                                select: function () {
                                    this.forceReloadOnSave = true;
                                }.bind(this)
                            }
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
            url: "/plugin/CoreShop/admin_country/save",
            method: "post",
            params: {
                data: Ext.encode(values),
                id : this.data.country.id
            },
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);
                    if (res.success) {
                        pimcore.helpers.showNotification(t("success"), t("coreshop_country_save_success"), "success");
                    } else {
                        pimcore.helpers.showNotification(t("error"), t("coreshop_country_save_error"),
                            "error", t(res.message));
                    }
                } catch(e) {
                    pimcore.helpers.showNotification(t("error"), t("coreshop_country_save_error"), "error");
                }
            }
        });
    }
});