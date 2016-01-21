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


pimcore.registerNS("pimcore.plugin.coreshop.customergroup.item");
pimcore.plugin.coreshop.customergroup.item = Class.create({

    initialize: function (parentPanel, data) {
        this.parentPanel = parentPanel;
        this.data = data;

        this.initPanel();
    },

    initPanel: function () {

        this.panel = new Ext.panel.Panel({
            title: this.data.name,
            closable: true,
            iconCls: "coreshop_icon_customer_groups",
            layout: "border",
            items : [this.getFormPanel()]
        });

        this.panel.on("beforedestroy", function () {
            delete this.parentPanel.panels["coreshop_customer_group_" + this.data.id];
        }.bind(this));

        this.parentPanel.getTabPanel().add(this.panel);
        this.parentPanel.getTabPanel().setActiveItem(this.panel);
    },

    activate : function() {
        this.parentPanel.getTabPanel().setActiveItem(this.panel);
    },

    getFormPanel : function()
    {
        var data = this.data;

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
                    labelWidth: 350,
                    defaultType: 'textfield',
                    defaults: {width: '100%'},
                    items :[
                        {
                            name: "name",
                            fieldLabel: t("name"),
                            width: 400,
                            value: data.name
                        },
                        {
                            xtype: "numberfield",
                            name: "discount",
                            fieldLabel: t("coreshop_customer_group_discount"),
                            width: 400,
                            value: data.discount,
                            decimalPrecision : 2,
                            step : 1
                        }
                    ]
                }
            ]
        });

        return this.formPanel;
    },

    save: function ()
    {
        var values = this.formPanel.getForm().getFieldValues();

        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_Customergroup/save",
            method: "post",
            params: {
                data: Ext.encode(values),
                id : this.data.id
            },
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);
                    if (res.success) {
                        pimcore.helpers.showNotification(t("success"), t("coreshop_customer_group_saved_successfully"), "success");
                    } else {
                        pimcore.helpers.showNotification(t("error"), t("coreshop_customer_group_saved_error"),
                            "error", t(res.message));
                    }
                } catch(e) {
                    pimcore.helpers.showNotification(t("error"), t("coreshop_customer_group_saved_error"), "error");
                }
            }
        });
    }
});