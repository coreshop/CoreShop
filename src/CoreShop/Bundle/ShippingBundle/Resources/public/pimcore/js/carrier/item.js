/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.carrier.item');
coreshop.carrier.item = Class.create(coreshop.resource.item, {

    iconCls: 'coreshop_icon_carrier',

    url: {
        save: '/admin/coreshop/carriers/save'
    },

    initialize: function (parentPanel, data, panelKey, type) {
        this.parentPanel = parentPanel;
        this.data = data;
        this.panelKey = panelKey;
        this.type = type;

        pimcore.globalmanager.get('coreshop_carrier_shipping_rules').load(function () {
            this.initPanel();
        }.bind(this));
    },

    getPanel: function () {
        return new Ext.TabPanel({
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
    },

    getItems: function () {
        return [
            this.getSettings(),
            this.getShippingLocationsAndCosts()
        ];
    },

    /**
     * Basic carrier Settings
     * @returns Ext.form.FormPanel
     */
    getSettings: function () {
        this.settingsForm = new Ext.form.Panel({
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
                value: this.data.name,
                required: true
            }, {
                xtype: 'textfield',
                name: 'label',
                fieldLabel: t('coreshop_carrier_label'),
                width: 250,
                value: this.data.label,
                required: true
            }, {
                xtype: 'textfield',
                name: 'trackingUrl',
                fieldLabel: t('coreshop_carrier_trackingUrl'),
                width: 250,
                value: this.data.trackingUrl
            }]
        });

        this.settingsForm.add(this.getMultishopSettings());

        return this.settingsForm;
    },

    getShippingRulesGrid: function () {
        this.shippingRuleGroupsStore = new Ext.data.Store({
            restful: false,
            idProperty: 'id',
            sorters: 'priority',
            data: this.data.shippingRules
        });

        this.shippingRuleGroupsGrid = Ext.create('Ext.grid.Panel', {
            store: this.shippingRuleGroupsStore,
            columns: [
                {
                    header: t('coreshop_carriers_shipping_rule'),
                    flex: 1,
                    dataIndex: 'shippingRule',
                    editor: new Ext.form.ComboBox({
                        store: pimcore.globalmanager.get('coreshop_carrier_shipping_rules'),
                        valueField: 'id',
                        displayField: 'name',
                        queryMode: 'local',
                        required: true
                    }),
                    renderer: function (shippingRule) {
                        var store = pimcore.globalmanager.get('coreshop_carrier_shipping_rules');
                        var pos = store.findExact('id', shippingRule);
                        if (pos >= 0) {
                            return store.getAt(pos).get('name');
                        }

                        return null;
                    }
                },
                {
                    header: t('priority'),
                    width: 200,
                    dataIndex: 'priority',
                    editor: {
                        xtype: 'numberfield',
                        decimalPrecision: 0,
                        required: true
                    }
                },
                {
                    xtype: 'actioncolumn',
                    width: 40,
                    items: [{
                        iconCls: 'pimcore_icon_delete',
                        tooltip: t('delete'),
                        handler: function (grid, rowIndex, colIndex) {
                            var rec = grid.getStore().getAt(rowIndex);

                            grid.getStore().remove(rec);
                        }
                    }]
                }
            ],
            tbar: [
                {
                    text: t('add'),
                    handler: function () {
                        this.shippingRuleGroupsStore.add({
                            id: null,
                            carrier: this.data.id,
                            shippingRule: null,
                            priority: 100
                        });
                    }.bind(this),
                    iconCls: 'pimcore_icon_add'
                }
            ],

            plugins: Ext.create('Ext.grid.plugin.CellEditing', {
                clicksToEdit: 1,
                listeners: {}
            })
        });

        return this.shippingRuleGroupsGrid;
    },

    getShippingLocationsAndCosts: function () {
        //Shipping locations and costs
        this.shippingLocationAndCosts = new Ext.form.Panel({
            iconCls: 'coreshop_carrier_costs_icon',
            title: t('coreshop_carrier_shipping_locations_and_costs'),
            bodyStyle: 'padding:10px;',
            autoScroll: true,
            border: false,
            items: [{
                xtype: 'checkbox',
                name: 'isFree',
                fieldLabel: t('coreshop_carrier_isFree'),
                width: 250,
                value: parseInt(this.data.isFree)
            }, {
                xtype: 'combo',
                fieldLabel: t('coreshop_carrier_tax_rule'),
                typeAhead: true,
                value: this.data.taxRule,
                mode: 'local',
                listWidth: 100,
                store: pimcore.globalmanager.get('coreshop_taxrulegroups'),
                displayField: 'name',
                valueField: 'id',
                forceSelection: true,
                triggerAction: 'all',
                name: 'taxRule',
                listeners: {
                    beforerender: function () {
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
            }, this.getShippingRulesGrid()]
        });

        return this.shippingLocationAndCosts;
    },

    getSaveData: function () {
        var data = {
            shippingRules: []
        };

        Ext.apply(data, this.settingsForm.getForm().getFieldValues());
        Ext.apply(data, this.shippingLocationAndCosts.getForm().getFieldValues());

        var ruleGroups = this.shippingRuleGroupsStore.getRange();

        Ext.each(ruleGroups, function (group) {
            var rule = {
                priority: group.get('priority'),
                shippingRule: group.get('shippingRule'),
                carrier: this.data.id
            };

            data.shippingRules.push(rule);
        }.bind(this));

        return data;
    }
});
