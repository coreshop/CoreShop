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
        panel.setActiveItem(this.tabPanel);
        panel.updateLayout();
    },

    activate : function() {
        this.parent.getTabPanel().setActiveItem(this.tabPanel);
    },

    /**
     * Basic carrier Settings
     * @returns Ext.form.FormPanel
     */
    getSettings: function () {
        this.settingsForm = new Ext.form.Panel({
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
        this.shippingLocationAndCosts = new Ext.form.Panel({
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
                width: 500,
                value: this.data.shippingMethod,
                triggerAction: "all",
                typeAhead: false,
                editable: false,
                forceSelection: true,
                mode: "local"
            }, {
                xtype:'combo',
                fieldLabel:t('coreshop_carrier_tax_rule_group'),
                typeAhead:true,
                value:this.data.taxRuleGroupId,
                mode:'local',
                listWidth:100,
                store:pimcore.globalmanager.get("coreshop_tax_rule_groups"),
                displayField:'name',
                valueField:'id',
                forceSelection:true,
                triggerAction:'all',
                name:'taxRuleGroupId'
            }, {
                fieldLabel: t("coreshop_carrier_rangeBehaviour"),
                name: "rangeBehaviour",
                value: this.data.rangeBehaviour,
                width: 500,
                xtype: "combo",
                store: [["largest",t("coreshop_carrier_rangeBehaviour_largest")],["deactivate",t("coreshop_carrier_rangeBehaviour_deactivate")]],
                triggerAction: "all",
                typeAhead: false,
                editable: false,
                forceSelection: true,
                mode: "local"
            }, this.getRangeGrid(), {height:40}, this.getZonesGrid()]
        });

        return this.shippingLocationAndCosts;
    },

    getRangeGrid : function() {
        var listeners = {};

        var modelName = 'coreshop.model.carrier.ranges';
        if (!Ext.ClassManager.get(modelName)) {
            Ext.define(modelName, {
                    extend: 'Ext.data.Model',
                    fields: ['id', 'delimiter1', 'delimiter2', 'price']
                }
            );
        }

        this.store = new Ext.data.Store({
            restful: false,
            idProperty: 'id',
            remoteSort: true,
            model : modelName,
            listeners: listeners,
            proxy: {
                type: 'ajax',
                url: '/plugin/CoreShop/admin_Carrier/get-range',
                reader: {
                    type: 'json',
                    rootProperty : 'data'
                },
                extraParams : {
                    carrier : this.data.id
                }
            }
        });

        var gridColumns = [
            {
                header: t("coreshop_carrier_delimeter1"),
                width: 200,
                dataIndex: 'delimiter1',
                editor: new Ext.form.TextField({})
            },
            {
                header: t("coreshop_carrier_delimeter2"),
                width: 200,
                dataIndex: 'delimiter2',
                editor: new Ext.form.TextField({})
            },
            {
                xtype:'actioncolumn',
                width:40,
                tooltip:t('delete'),
                icon:"/pimcore/static6/img/icon/cross.png",
                handler:function (grid, rowIndex) {
                    grid.getStore().removeAt(rowIndex);
                }.bind(this)
            }
            /*{
                header: t("coreshop_carrier_price"),
                width: 200,
                dataIndex: 'price',
                editor: new Ext.form.TextField({})
            }*/
        ];

        this.cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1,
            listeners: {}
        });


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
            selModel: Ext.create('Ext.selection.RowModel', {}),
            tbar: [

                {
                    text: t('add'),
                    handler: this.addRangeRow.bind(this),
                    iconCls: "pimcore_icon_add"
                }
            ],
            plugins: [
                this.cellEditing
            ]
        };

        this.grid = Ext.create('Ext.grid.Panel', gridConfig);

        this.store.load();

        return this.grid;
    },

    getZonesGrid : function() {
        var listeners = {};
        var modelFields = [
            'range', 'rangeId'
        ];

        var gridColumns = [{
            header : t('coreshop_carrier_range'),
            width : 200,
            dataIndex : 'range'
        }];

        pimcore.globalmanager.get("coreshop_zones").getRange().forEach(function(item) {
            var fieldName = 'zone_' + item.get("id");

            modelFields.push(fieldName);
            gridColumns.push({
                header: item.get("name"),
                width: 200,
                dataIndex: fieldName,
                editor: item.get("active") ? new Ext.form.TextField({}) : false,
                disabled: !item.get("active")
            });
        });

        var modelName = 'coreshop.model.carrier.zones';
        if (!Ext.ClassManager.get(modelName)) {
            Ext.define(modelName, {
                    extend: 'Ext.data.Model',
                    fields: modelFields
                }
            );
        }

        this.zonesStore = new Ext.data.Store({
            restful: false,
            idProperty: 'id',
            remoteSort: true,
            model : modelName,
            listeners: listeners,
            proxy: {
                type: 'ajax',
                url: '/plugin/CoreShop/admin_Carrier/get-prices',
                reader: {
                    type: 'json',
                    rootProperty : 'data'
                },
                extraParams : {
                    carrier : this.data.id
                }
            }
        });


        this.cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1,
            listeners: {}
        });

        var gridConfig = {
            frame: false,
            store: this.zonesStore,
            border: true,
            columns: gridColumns,
            loadMask: true,
            columnLines: true,
            stripeRows: true,
            trackMouseOver: true,
            viewConfig: {
                forceFit: false
            },
            selModel: Ext.create('Ext.selection.RowModel', {}),
            plugins: [
                this.cellEditing
            ]
        };

        this.zonesGrid = Ext.create('Ext.grid.Panel', gridConfig);

        this.zonesStore.load();

        return this.zonesGrid;
    },

    addRangeRow : function() {
        var model = this.store.getModel();
        var newRecord = new model({
            'delimiter1' : 0,
            'delimiter2' : 0,
            'price' : 0
        });

        this.store.add(newRecord);
        this.grid.getView().refresh();

        this.zonesGrid.setDisabled(true);
    },

    getDimensions : function() {
        this.dimensionsForm = new Ext.form.Panel({
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

        data["range"] = Ext.pluck(this.store.getRange(), 'data');
        data["deliveryPrices"] = Ext.pluck(this.zonesStore.getRange(), 'data');

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
        this.parent.getTree().getStore().reload();
        pimcore.helpers.showNotification(t("success"), t("coreshop_carrier_saved_successfully"), "success");

        this.zonesGrid.setDisabled(false);
        this.zonesStore.load();

        this.store.load();
    }
});
