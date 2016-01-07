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


pimcore.registerNS("pimcore.plugin.coreshop.orderstate.item");
pimcore.plugin.coreshop.orderstate.item = Class.create({

    initialize: function (parentPanel, data) {
        this.parentPanel = parentPanel;
        this.data = data;

        this.initPanel();
    },

    initPanel: function () {

        this.panel = new Ext.panel.Panel({
            title: this.data.name,
            closable: true,
            iconCls: "coreshop_icon_order_states",
            layout: "border",
            items : [this.getFormPanel()]
        });

        this.panel.on("beforedestroy", function () {
            delete this.parentPanel.panels["coreshop_order_state" + this.data.id];
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
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 300},
                    items :[
                        {
                            xtype: "textfield",
                            name: "name",
                            fieldLabel: t("name"),
                            width: 250,
                            value: data.name
                        }, {
                            xtype: "checkbox",
                            name: "accepted",
                            fieldLabel: t("coreshop_order_state_accepted"),
                            width: 250,
                            checked: parseInt(data.accepted)
                        }, {
                            xtype: "checkbox",
                            name: "shipped",
                            fieldLabel: t("coreshop_order_state_shipped"),
                            width: 250,
                            checked: parseInt(data.shipped)
                        }, {
                            xtype: "checkbox",
                            name: "paid",
                            fieldLabel: t("coreshop_order_state_paid"),
                            width: 250,
                            checked: parseInt(data.paid)
                        }, {
                            xtype: "checkbox",
                            name: "invoice",
                            fieldLabel: t("coreshop_order_state_invoice"),
                            width: 250,
                            checked: parseInt(data.invoice)
                        }, {
                            xtype: "checkbox",
                            name: "email",
                            fieldLabel: t("coreshop_order_state_email"),
                            width: 250,
                            checked: parseInt(data.email)
                        }, {
                            fieldLabel: t("coreshop_order_state_emailDocument"),
                            name: "emailDocument",
                            cls: "input_drop_target",
                            value: data.emailDocument,
                            xtype: "textfield",
                            listeners: {
                                "render": function (el) {
                                    new Ext.dd.DropZone(el.getEl(), {
                                        reference: this,
                                        ddGroup: "element",
                                        getTargetFromEvent: function(e) {
                                            return this.getEl();
                                        }.bind(el),

                                        onNodeOver : function(target, dd, e, data) {
                                            data = data.records[0].data;

                                            if (data.elementType == "document") {
                                                return Ext.dd.DropZone.prototype.dropAllowed;
                                            }
                                            return Ext.dd.DropZone.prototype.dropNotAllowed;
                                        },

                                        onNodeDrop : function (target, dd, e, data) {
                                            data = data.records[0].data;

                                            if (data.elementType == "document") {
                                                this.setValue(data.path);
                                                return true;
                                            }
                                            return false;
                                        }.bind(el)
                                    });
                                }
                            }
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
            url: "/plugin/CoreShop/admin_OrderStates/save",
            method: "post",
            params: {
                data: Ext.encode(values),
                id : this.data.id
            },
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);
                    if (res.success) {
                        pimcore.helpers.showNotification(t("success"), t("coreshop_order_state_saved_successfully"), "success");
                    } else {
                        pimcore.helpers.showNotification(t("error"), t("coreshop_order_state_saved_error"),
                            "error", t(res.message));
                    }
                } catch(e) {
                    pimcore.helpers.showNotification(t("error"), t("coreshop_order_state_saved_error"), "error");
                }
            }
        });
    }
});