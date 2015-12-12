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

pimcore.registerNS("pimcore.plugin.coreshop.settings");
pimcore.plugin.coreshop.settings= Class.create({

    initialize: function () {

        this.getData();
    },

    getData: function () {
        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_settings/get",
            success: function (response) {

                this.data = Ext.decode(response.responseText);

                this.getTabPanel();

            }.bind(this)
        });
    },

    getValue: function (key) {

        var nk = key.split("\.");
        var current = this.data.values;

        for (var i = 0; i < nk.length; i++) {
            if (current[nk[i]]) {
                current = current[nk[i]];
            } else {
                current = null;
                break;
            }
        }

        if (typeof current != "object" && typeof current != "array" && typeof current != "function") {
            return current;
        }

        return "";
    },

    getTabPanel: function () {

        if (!this.panel) {
            this.panel = Ext.create('Ext.panel.Panel', {
                id: "coreshop_settings",
                title: t("coreshop_settings"),
                iconCls: "coreshop_icon_settings",
                border: false,
                layout: "fit",
                closable:true
            });

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.setActiveItem("coreshop_settings");


            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("coreshop_settings");
            }.bind(this));


            this.layout = Ext.create('Ext.form.Panel', {
                bodyStyle:'padding:20px 5px 20px 5px;',
                border: false,
                autoScroll: true,
                forceLayout: true,
                defaults: {
                    forceLayout: true
                },
                fieldDefaults: {
                    labelWidth: 250
                },
                buttons: [
                    {
                        text: "Save",
                        handler: this.save.bind(this),
                        iconCls: "pimcore_icon_apply"
                    }
                ],
                items: [
                    {
                        xtype:'fieldset',
                        title: t('coreshop_base'),
                        collapsible: true,
                        collapsed: true,
                        autoHeight:true,
                        labelWidth: 250,
                        defaultType: 'textfield',
                        defaults: {width: 600},
                        items :[
                            {
                                xtype:'combo',
                                fieldLabel:t('coreshop_base_currency'),
                                typeAhead:true,
                                value:this.getValue("base.base-currency"),
                                mode:'local',
                                listWidth:100,
                                store:pimcore.globalmanager.get("coreshop_currencies"),
                                displayField:'name',
                                valueField:'id',
                                forceSelection:true,
                                triggerAction:'all',
                                hiddenName:'base.base-currency',
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
                    },
                    {
                        xtype:'fieldset',
                        title: t('coreshop_product'),
                        collapsible: true,
                        collapsed: true,
                        autoHeight:true,
                        labelWidth: 250,
                        defaultType: 'textfield',
                        defaults: {width: 600},
                        items :[
                            {
                                fieldLabel: t("coreshop_default_image"),
                                name: "product.default-image",
                                cls: "input_drop_target",
                                value: this.getValue("product.default-image"),
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
                                                return Ext.dd.DropZone.prototype.dropAllowed;
                                            },

                                            onNodeDrop : function (target, dd, e, data) {
                                                if (data.node.attributes.elementType == "asset") {
                                                    this.setValue(data.node.attributes.path);
                                                    return true;
                                                }
                                                return false;
                                            }.bind(el)
                                        });
                                    }
                                }
                            },
                            {
                                fieldLabel: t('coreshop_product_daysasnew'),
                                name: 'product.days-as-new',
                                value: this.getValue("product.days-as-new"),
                                xtype: "spinnerfield",
                                enableKeyEvents: true
                            }
                        ]
                    },
                    {
                        xtype:'fieldset',
                        title: t('coreshop_category'),
                        collapsible: true,
                        collapsed: true,
                        autoHeight:true,
                        labelWidth: 250,
                        defaultType: 'textfield',
                        defaults: {width: 600},
                        items :[
                            {
                                fieldLabel: t("coreshop_default_image"),
                                name: "category.default-image",
                                cls: "input_drop_target",
                                value: this.getValue("category.default-image"),
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
                                                return Ext.dd.DropZone.prototype.dropAllowed;
                                            },

                                            onNodeDrop : function (target, dd, e, data) {
                                                if (data.node.attributes.elementType == "asset") {
                                                    this.setValue(data.node.attributes.path);
                                                    return true;
                                                }
                                                return false;
                                            }.bind(el)
                                        });
                                    }
                                }
                            }
                        ]
                    },
                    {
                        xtype:'fieldset',
                        title: t('coreshop_template'),
                        collapsible: true,
                        collapsed: true,
                        autoHeight:true,
                        labelWidth: 250,
                        defaultType: 'textfield',
                        defaults: {width: 600},
                        items :[
                            {
                                fieldLabel: t('coreshop_template_name'),
                                name: 'template.name',
                                value: this.getValue("template.name"),
                                enableKeyEvents: true
                            },
                        ]
                    }
                ]
            });

            this.panel.add(this.layout);

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    activate: function () {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.activate("coreshop_settings");
    },

    save: function () {
        var values = this.layout.getForm().getFieldValues();

        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_settings/set",
            method: "post",
            params: {
                data: Ext.encode(values)
            },
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);
                    if (res.success) {
                        pimcore.helpers.showNotification(t("success"), t("coreshop_settings_save_success"), "success");
                    } else {
                        pimcore.helpers.showNotification(t("error"), t("coreshop_settings_save_error"),
                            "error", t(res.message));
                    }
                } catch(e) {
                    pimcore.helpers.showNotification(t("error"), t("coreshop_settings_save_error"), "error");
                }
            }
        });
    }
});