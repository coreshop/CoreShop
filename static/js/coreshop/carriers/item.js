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


pimcore.registerNS("pimcore.plugin.coreshop.carrier.item");
pimcore.plugin.coreshop.carrier.item = Class.create({

    /**
     * pimcore.plugin.coreshop.carrier.panel
     */
    parent: {},


    /**
     * constructor
     * @param parent
     * @param data
     */
    initialize: function(parent, data) {
        this.parent = parent;
        this.data = data;
        this.currentIndex = 0;

        this.tabPanel = new Ext.TabPanel({
            activeTab: 0,
            title: this.data.name,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            id: "pimcore_targeting_panel_" + this.data.id,
            buttons: [{
                text: t("save"),
                iconCls: "pimcore_icon_apply",
                handler: this.save.bind(this)
            }],
            items: [
                this.getSettings(),
                this.getShippingLocationsAndCosts(),
                this.getDimensions()
            ]
        });

        // ...
        var panel = this.parent.getTabPanel();
        panel.add(this.tabPanel);
        panel.activate(this.tabPanel);
        panel.doLayout();
    },

    /**
     * Basic carrier Settings
     * @returns Ext.form.FormPanel
     */
    getSettings: function () {
        this.settingsForm = new Ext.form.FormPanel({
            layout: "pimcoreform",
            iconCls: "coreshop_carrier_settings_icon",
            title: t("settings"),
            bodyStyle: "padding:10px;",
            autoScroll: true,
            border:false,
            items: [{
                xtype: "textfield",
                name: "label",
                fieldLabel: t("coreshop_carrier_label"),
                width: 250,
                value: this.data.label
            }, {
                xtype: "textfield",
                name: "delay",
                fieldLabel: t("coreshop_carrier_delay"),
                width: 250,
                value: this.data.delay
            }, {
                xtype: "spinnerfield",
                name: "grade",
                fieldLabel: t("coreshop_carrier_grade"),
                width: 250,
                value: this.data.grade
            }, {
                fieldLabel: t("coreshop_carrier_image"),
                name: "image",
                cls: "input_drop_target",
                value: this.data.image,
                width: 300,
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
                                if (data.node.attributes.elementType == "asset" && data.node.attributes.type == "image") {
                                    return Ext.dd.DropZone.prototype.dropAllowed;
                                }
                                return Ext.dd.DropZone.prototype.dropNotAllowed;
                            },

                            onNodeDrop : function (target, dd, e, data) {
                                if (data.node.attributes.elementType == "asset" && data.node.attributes.type == "image") {
                                    this.setValue(data.node.attributes.path);
                                    return true;
                                }
                                return false;
                            }.bind(el)
                        });
                    }
                }
            }, {
                xtype: "textfield",
                name: "trackingCode",
                fieldLabel: t("coreshop_carrier_trackingCode"),
                width: 250,
                value: this.data.trackingCode
            }]
        });

        return this.settingsForm;
    },

    getShippingLocationsAndCosts : function() {
        //Shipping locations and costs
        this.shippingLocationAndCosts = new Ext.form.FormPanel({
            layout: "pimcoreform",
            iconCls: "coreshop_carrier_costs_icon",
            title: t("coreshop_carrier_shipping_locations_and_costs"),
            bodyStyle: "padding:10px;",
            autoScroll: true,
            border:false,
            items: [{
                xtype: "checkbox",
                name: "isFree",
                fieldLabel: t("coreshop_carrier_isFree"),
                width: 250,
                value: this.data.label
            }, {
                xtype: "combo",
                store: [["price",t("coreshop_carrier_shippingMethod_price")],["weight",t("coreshop_carrier_shippingMethod_weight")]],
                name: "shippingMethod",
                fieldLabel: t("coreshop_carrier_shippingMethod"),
                width: 250,
                value: this.data.shippingMethod,
                triggerAction: "all",
                typeAhead: false,
                editable: false,
                forceSelection: true,
                mode: "local"
            }, {
                xtype: "textfield",
                name: "tax",
                fieldLabel: t("coreshop_carrier_tax"),
                width: 250,
                value: this.data.tax
            }, {
                fieldLabel: t("coreshop_carrier_rangeBehaviour"),
                name: "rangeBehaviour",
                value: this.data.rangeBehaviour,
                width: 250,
                xtype: "combo",
                store: [["largest",t("coreshop_carrier_rangeBehaviour_largest")],["deactivate",t("coreshop_carrier_rangeBehaviour_deactivate")]],
                triggerAction: "all",
                typeAhead: false,
                editable: false,
                forceSelection: true,
                mode: "local"
            }, this.getRangeGrid()]
        });

        return this.shippingLocationAndCosts;
    },

    getRangeGrid : function() {
        this.fields = ['id', 'delimiter1', 'delimiter2', 'price'];

        var readerFields = [];
        for (var i = 0; i < this.fields.length; i++) {
            readerFields.push({name: this.fields[i], allowBlank: true});
        }

        var proxy = new Ext.data.HttpProxy({
            url: '/plugin/CoreShop/admin_Carrier/get-range',
            method: 'post'
        });
        var reader = new Ext.data.JsonReader({
            totalProperty: 'total',
            successProperty: 'success',
            root: 'data'
        },readerFields);

        var writer = null;
        var listeners = {};
        if(this.enableEditor) {
            writer = new Ext.data.JsonWriter();
            listeners.write = function(store, action, result, response, rs) {};
            listeners.exception = function (conn, mode, action, request, response, store) {
                if(action == "update") {
                    Ext.MessageBox.alert(t('error'),
                        t('cannot_save_object_please_try_to_edit_the_object_in_detail_view'));
                    this.store.rejectChanges();
                }
            }.bind(this);
        }

        this.store = new Ext.data.Store({
            restful: false,
            idProperty: 'id',
            remoteSort: true,
            proxy: proxy,
            reader: reader,
            writer: writer,
            listeners: listeners,
            baseParams: {
                "carrier" : this.data.id
            }
        });

        var gridColumns = [];

        gridColumns.push({header: t("coreshop_carrier_delimeter1"), width: 200, dataIndex: 'delimiter1',editor: new Ext.form.TextField({})});
        gridColumns.push({header: t("coreshop_carrier_delimeter2"), width: 200, dataIndex: 'delimiter2', editor: new Ext.form.TextField({})});
        gridColumns.push({header: t("coreshop_carrier_price"), width: 200, dataIndex: 'price', editor: new Ext.form.TextField({})});

        var gridConfig = {
            frame: false,
            store: this.store,
            border: true,
            columns: gridColumns,
            loadMask: true,
            columnLines: true,
            stripeRows: true,
            trackMouseOver: true,
            viewConfig: {
                forceFit: false
            },
            sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
            tbar: [

                {
                    text: t('add'),
                    handler: this.addRangeRow.bind(this),
                    iconCls: "pimcore_icon_add"
                }
            ]
        };

        this.grid = new Ext.grid.EditorGridPanel(gridConfig);

        this.store.load();

        return this.grid;
    },

    addRangeRow : function() {
        var newRecord = new this.store.recordType({

        });

        this.store.add(newRecord);
    },

    getDimensions : function() {
        this.dimensionsForm = new Ext.form.FormPanel({
            layout: "pimcoreform",
            iconCls: "coreshop_carrier_dimensions_icon",
            title: t("coreshop_carrier_dimensions"),
            bodyStyle: "padding:10px;",
            autoScroll: true,
            border:false,
            items: [{
                xtype: "textfield",
                name: "maxHeight",
                fieldLabel: t("coreshop_carrier_maxHeight"),
                width: 250,
                value: this.data.maxHeight
            }, {
                xtype: "textfield",
                name: "maxWidth",
                fieldLabel: t("coreshop_carrier_maxWidth"),
                width: 250,
                value: this.data.maxWidth
            }, {
                xtype: "textfield",
                name: "maxDepth",
                fieldLabel: t("coreshop_carrier_maxDepth"),
                width: 250,
                value: this.data.maxDepth
            }, {
                xtype: "textfield",
                name: "maxWeight",
                fieldLabel: t("coreshop_carrier_maxWeight"),
                width: 250,
                value: this.data.maxWeight
            }]
        });

        return this.dimensionsForm;
    },

    /**
     * save config
     * @todo
     */
    save: function () {

        var data = {
            settings : {}
        };

        Ext.apply(data.settings, this.settingsForm.getForm().getFieldValues());
        Ext.apply(data.settings, this.shippingLocationAndCosts.getForm().getFieldValues());
        Ext.apply(data.settings, this.dimensionsForm.getForm().getFieldValues());

        data["range"] = Ext.pluck(this.store.data.items, 'data');

        // send data
        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_Carrier/save",
            params: {
                id: this.data.id,
                data: Ext.encode(data)
            },
            method: "post",
            success: this.saveOnComplete.bind(this)
        });
    },

    /**
     * saved
     */
    saveOnComplete: function () {
        this.parent.getTree().getRootNode().reload();
        pimcore.helpers.showNotification(t("success"), t("coreshop_carrier_saved_successfully"), "success");
    }
});
