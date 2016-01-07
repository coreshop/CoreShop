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


pimcore.registerNS("pimcore.plugin.coreshop.zones.zone");
pimcore.plugin.coreshop.zones.zone = Class.create({

    initialize: function (parentPanel, id) {
        this.parentPanel = parentPanel;
        this.id = id;
        this.data = undefined;

        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_zone/get-zone",
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
            title: this.data.zone.name,
            closable: true,
            layout: "border",
            iconCls: "coreshop_icon_zone",
            items : [this.getFormPanel()]
        });

        this.panel.on("beforedestroy", function () {
            delete this.parentPanel.panels["coreshop_zone_" + this.id];
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
                            fieldLabel: t("coreshop_zone_name"),
                            name: "name",
                            value: this.data.zone.name
                        },
                        {
                            xtype : 'checkbox',
                            fieldLabel: t("coreshop_zone_active"),
                            name: "active",
                            checked: intval(this.data.zone.active) === 1
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
            url: "/plugin/CoreShop/admin_zone/save",
            method: "post",
            params: {
                data: Ext.encode(values),
                id : this.data.zone.id
            },
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);
                    if (res.success) {
                        pimcore.helpers.showNotification(t("success"), t("coreshop_zone_save_success"), "success");
                    } else {
                        pimcore.helpers.showNotification(t("error"), t("coreshop_zone_save_error"),
                            "error", t(res.message));
                    }
                } catch(e) {
                    pimcore.helpers.showNotification(t("error"), t("coreshop_zone_save_error"), "error");
                }
            }
        });
    }
});