/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.carriers.item');
pimcore.plugin.coreshop.carriers.item = Class.create(pimcore.plugin.coreshop.abstract.item, {

    iconCls : 'coreshop_icon_carrier',

    url : {
        save : '/plugin/CoreShop/admin_carrier/save'
    },

    initialize: function (parentPanel, data, panelKey, type) {
        this.parentPanel = parentPanel;
        this.data = data;
        this.panelKey = panelKey;
        this.type = type;
        this.ranges = [];

        pimcore.globalmanager.get('coreshop_zones').load(function () {
            Ext.Ajax.request({
                url: '/plugin/CoreShop/admin_carrier/get-range',
                params : {
                    carrier : this.data.id
                },
                success: function (response) {
                    this.ranges = Ext.decode(response.responseText).data;

                    this.initPanel();
                }.bind(this)
            });
        }.bind(this));
    },

    getPanel: function () {
        var panel = new Ext.TabPanel({
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

        return panel;
    },

    getItems : function () {
        return [
            this.getSettings(),
            this.getShippingLocationsAndCosts(),
            this.getDimensions()
        ];
    },

    /**
     * Basic carrier Settings
     * @returns Ext.form.FormPanel
     */
    getSettings: function () {
        this.settingsForm = new Ext.form.Panel({
            iconCls: 'coreshop_carrier_settings_icon',
            title: t('settings'),
            bodyStyle: 'padding:10px;',
            autoScroll: true,
            border:false,
            items: [{
                xtype: 'textfield',
                name: 'label',
                fieldLabel: t('coreshop_carrier_label'),
                width: 250,
                value: this.data.label
            }, {
                xtype: 'textfield',
                name: 'delay',
                fieldLabel: t('coreshop_carrier_delay'),
                width: 250,
                value: this.data.delay
            }, {
                xtype: 'spinnerfield',
                name: 'grade',
                fieldLabel: t('coreshop_carrier_grade'),
                width: 250,
                value: this.data.grade
            }, {
                fieldLabel: t('coreshop_carrier_image'),
                name: 'image',
                cls: 'input_drop_target',
                value: this.data.image ? this.data.image.id : null,
                width: 300,
                xtype: 'textfield',
                listeners: {
                    render: function (el) {
                        new Ext.dd.DropZone(el.getEl(), {
                            reference: this,
                            ddGroup: 'element',
                            getTargetFromEvent: function (e) {
                                return this.getEl();
                            }.bind(el),

                            onNodeOver : function (target, dd, e, data) {
                                data = data.records[0].data;

                                if (data.elementType == 'asset') {
                                    return Ext.dd.DropZone.prototype.dropAllowed;
                                }

                                return Ext.dd.DropZone.prototype.dropNotAllowed;
                            },

                            onNodeDrop : function (target, dd, e, data) {
                                data = data.records[0].data;

                                if (data.elementType == 'asset') {
                                    this.setValue(data.id);
                                    return true;
                                }

                                return false;
                            }.bind(el)
                        });
                    }
                }
            }, {
                xtype: 'textfield',
                name: 'trackingUrl',
                fieldLabel: t('coreshop_carrier_trackingUrl'),
                width: 250,
                value: this.data.trackingUrl
            }]
        });

        return this.settingsForm;
    },

    getShippingLocationsAndCosts : function () {
        //Shipping locations and costs
        this.shippingLocationAndCosts = new Ext.form.Panel({
            iconCls: 'coreshop_carrier_costs_icon',
            title: t('coreshop_carrier_shipping_locations_and_costs'),
            bodyStyle: 'padding:10px;',
            autoScroll: true,
            border:false,
            items: [{
                xtype: 'checkbox',
                name: 'isFree',
                fieldLabel: t('coreshop_carrier_isFree'),
                width: 250,
                value: parseInt(this.data.isFree)
            }, {
                xtype: 'combo',
                store: [['price', t('coreshop_carrier_shippingMethod_price')], ['weight', t('coreshop_carrier_shippingMethod_weight')]],
                name: 'shippingMethod',
                fieldLabel: t('coreshop_carrier_shippingMethod'),
                width: 500,
                value: this.data.shippingMethod,
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                mode: 'local'
            }, {
                xtype:'combo',
                fieldLabel:t('coreshop_carrier_tax_rule_group'),
                typeAhead:true,
                value:this.data.taxRuleGroupId,
                mode:'local',
                listWidth:100,
                store:pimcore.globalmanager.get('coreshop_taxrulegroups'),
                displayField:'name',
                valueField:'id',
                forceSelection:true,
                triggerAction:'all',
                name:'taxRuleGroupId',
                listeners : {
                    beforerender : function () {
                        if (!this.getStore().isLoaded() && !this.getStore().isLoading())
                            this.getStore().load();
                    }
                }
            }, {
                fieldLabel: t('coreshop_carrier_rangeBehaviour'),
                name: 'rangeBehaviour',
                value: this.data.rangeBehaviour,
                width: 500,
                xtype: 'combo',
                store: [['largest', t('coreshop_carrier_rangeBehaviour_largest')], ['deactivate', t('coreshop_carrier_rangeBehaviour_deactivate')]],
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                mode: 'local'
            }, this.getGrid()]
        });

        return this.shippingLocationAndCosts;
    },

    getGrid : function () {
        if (!this.shippingCostRangesGrid) {

            this.shippingCostRangesStore = new Ext.data.Store({
                restful: false,
                idProperty: 'id',
                proxy: {
                    type: 'ajax',
                    url: '/plugin/CoreShop/admin_carrier/get-range-zone',
                    reader: {
                        type: 'json',
                        rootProperty: 'data'
                    },
                    extraParams: {
                        carrier: this.data.id
                    }
                }
            });
            this.shippingCostRangesStore.load();

            this.shippingCostRangesGrid = Ext.create('Ext.grid.Panel', {
                store: this.shippingCostRangesStore,
                columns: [
                    {
                        width: 200,
                        dataIndex: 'name',
                        items: [
                            {
                                xtype: 'textfield',
                                value: t('coreshop_carrier_delimeter1'),
                                disabled: true,
                                width: 200,
                                style: 'margin-bottom:0'
                            },
                            {
                                xtype: 'textfield',
                                value: t('coreshop_carrier_delimeter2'),
                                disabled: true,
                                width: 200
                            }
                        ]
                    }
                ],

                dockedItems: [{
                    xtype: 'toolbar',
                    items: [
                        {
                            text: t('add'),
                            handler: function () {
                                var range = {
                                    id: Ext.id(),
                                    delimiter1: this.ranges.length > 0 ? parseInt(this.ranges[this.ranges.length - 1].delimiter2) : 0,
                                    delimiter2: this.ranges.length > 0 ? parseInt(this.ranges[this.ranges.length - 1].delimiter2) + 1 : 0
                                };

                                this.addRangeColumn(range);
                                this.ranges.push(range);
                            }.bind(this)
                        }
                    ]
                }],

                plugins: Ext.create('Ext.grid.plugin.CellEditing', {
                    clicksToEdit: 1,
                    listeners: {}
                })
            });

            this.ranges.forEach(this.addRangeColumn.bind(this));
        }

        return this.shippingCostRangesGrid;
    },

    addRangeColumn : function (range) {
        if (this.shippingCostRangesGrid) {

            var itemId = Ext.id();
            var item = {
                width : 200,
                dataIndex: 'range_' + range.id,
                editor: new Ext.form.TextField({}),
                id : itemId,
                menuDisabled : true,
                items : [
                    {
                        xtype : 'numberfield',
                        width: 200,
                        style : 'margin-bottom:0',
                        value : range.delimiter1,
                        listeners : {
                            change : function (txtField, newValue) {
                                range.delimiter1 = newValue;
                            }
                        },
                        mouseWheelEnabled: false,
                        allowDecimals : false
                    },
                    {
                        xtype : 'numberfield',
                        width: 200,
                        value : range.delimiter2,
                        listeners : {
                            change : function (txtField, newValue) {
                                range.delimiter2 = newValue;
                            }
                        },
                        mouseWheelEnabled: false,
                        allowDecimals : false
                    },
                    {
                        xtype : 'button',
                        iconCls: 'pimcore_icon_delete',
                        cls : 'coreshop_carrier_delete',
                        handler : function () {
                            var column = this.shippingCostRangesGrid.headerCt.getComponent(itemId);
                            this.shippingCostRangesGrid.headerCt.remove(column);
                            this.shippingCostRangesGrid.getView().refresh();

                            for (var i = 0; i < this.ranges.length; i++) {
                                if (this.ranges[i].id === range.id) {
                                    this.ranges.splice(i, 1);
                                }
                            }
                        }.bind(this)
                    }
                ]
            };

            this.shippingCostRangesGrid.headerCt.insert(this.shippingCostRangesGrid.columns.length, item);
            this.shippingCostRangesGrid.columns.push(item);
            this.shippingCostRangesGrid.getView().refresh();
        }
    },

    addRangeRow : function () {
        var model = this.store.getModel();
        var newRecord = new model({
            delimiter1 : 0,
            delimiter2 : 0,
            price : 0
        });

        this.store.add(newRecord);
        this.grid.getView().refresh();

        this.zonesGrid.setDisabled(true);
    },

    getDimensions : function () {
        this.dimensionsForm = new Ext.form.Panel({
            iconCls: 'coreshop_carrier_dimensions_icon',
            title: t('coreshop_carrier_dimensions'),
            bodyStyle: 'padding:10px;',
            autoScroll: true,
            border:false,
            items: [{
                xtype: 'textfield',
                name: 'maxHeight',
                fieldLabel: t('coreshop_carrier_maxHeight'),
                width: 250,
                value: this.data.maxHeight
            }, {
                xtype: 'textfield',
                name: 'maxWidth',
                fieldLabel: t('coreshop_carrier_maxWidth'),
                width: 250,
                value: this.data.maxWidth
            }, {
                xtype: 'textfield',
                name: 'maxDepth',
                fieldLabel: t('coreshop_carrier_maxDepth'),
                width: 250,
                value: this.data.maxDepth
            }, {
                xtype: 'textfield',
                name: 'maxWeight',
                fieldLabel: t('coreshop_carrier_maxWeight'),
                width: 250,
                value: this.data.maxWeight
            }]
        });

        return this.dimensionsForm;
    },

    getSaveData : function () {
        var data = {
            settings : {}
        };

        Ext.apply(data.settings, this.settingsForm.getForm().getFieldValues());
        Ext.apply(data.settings, this.shippingLocationAndCosts.getForm().getFieldValues());
        Ext.apply(data.settings, this.dimensionsForm.getForm().getFieldValues());

        data['range'] = Ext.clone(this.ranges);

        var zonePrices = this.shippingCostRangesStore.getRange();

        //data['range'] = Ext.pluck(this.store.getRange(), 'data');
        //data['deliveryPrices'] = Ext.pluck(this.zonesStore.getRange(), 'data');

        data.range.forEach(function (range) {
            range.zones = [];

            zonePrices.forEach(function (zone) {
                if (zone.data.hasOwnProperty('range_' + range.id)) {
                    range.zones[zone.data.zone] = zone.data['range_' + range.id];
                } else {
                    range.zones[zone.data.zone] = 0;
                }
            });
        });

        return {
            data : Ext.encode(data)
        };
    },

    postSave : function (result) {
        this.shippingCostRangesGrid.destroy();
        this.shippingCostRangesGrid = null;

        this.ranges = result.ranges;

        this.shippingLocationAndCosts.add(this.getGrid());
    }
});
