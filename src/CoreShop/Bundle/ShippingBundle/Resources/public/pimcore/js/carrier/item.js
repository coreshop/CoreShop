/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
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

        var store = Ext.create('store.coreshop_carrier_shipping_rules');

        store.load(function () {
            this.initPanel();
        }.bind(this));
    },

    getPanel: function () {
        return new Ext.TabPanel({
            activeTab: 0,
            title: this.data.identifier,
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

    getTitleText: function () {
        return this.data.identifier;
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
        var data = this.data,
            langTabs = [];

        Ext.each(pimcore.settings.websiteLanguages, function (lang) {
            var tab = {
                title: pimcore.available_languages[lang],
                iconCls: 'pimcore_icon_language_' + lang.toLowerCase(),
                layout: 'form',
                items: [{
                    xtype: 'textfield',
                    name: 'translations.' + lang + '.title',
                    fieldLabel: t('title'),
                    value: data.translations && data.translations[lang] ? data.translations[lang].title : '',
                    required: true
                }, {
                    xtype: 'textarea',
                    name: 'translations.' + lang + '.description',
                    fieldLabel: t('description'),
                    width: 400,
                    value: data.translations && data.translations[lang] ? data.translations[lang].description : ''
                }]
            };

            langTabs.push(tab);
        });

        this.settingsForm = new Ext.form.Panel({
            iconCls: 'coreshop_icon_settings',
            title: t('settings'),
            bodyStyle: 'padding:10px;',
            autoScroll: true,
            border: false,
            items: [{
                xtype: 'fieldset',
                autoHeight: true,
                labelWidth: 350,
                defaultType: 'textfield',
                defaults: {width: '100%'},
                items: [
                    {
                        xtype: 'textfield',
                        name: 'identifier',
                        fieldLabel: t('coreshop_identifier'),
                        value: data.identifier,
                        required: true
                    }, {
                        xtype: 'textfield',
                        name: 'trackingUrl',
                        fieldLabel: t('coreshop_carrier_trackingUrl'),
                        value: data.trackingUrl
                    },
                    this.getLogoSelect().getLayoutEdit(),
                    {
                        xtype: 'tabpanel',
                        activeTab: 0,
                        defaults: {
                            autoHeight: true,
                            bodyStyle: 'padding:10px;'
                        },
                        items: langTabs
                    }
                ]
            }]
        });

        return this.settingsForm;
    },

    getLogoSelect: function () {
        return new coreshop.object.elementHref({
            id: this.data.logo,
            type: 'asset',
            subtype: 'image'
        }, {
            classes: [],
            assetsAllowed: true,
            name: 'logo',
            title: t('coreshop_logo')
        });
    },

    getShippingRulesGrid: function () {
        this.shippingRuleGroupsStore = new Ext.data.Store({
            restful: false,
            idProperty: 'id',
            sorters: 'priority',
            data: this.data.shippingRules
        });

        var store = Ext.create('store.coreshop_carrier_shipping_rules');
        store.load();

        this.shippingRuleGroupsGrid = Ext.create('Ext.grid.Panel', {
            store: this.shippingRuleGroupsStore,
            columns: [
                {
                    header: t('coreshop_carriers_shipping_rule'),
                    flex: 2,
                    dataIndex: 'shippingRule',
                    editor: new Ext.form.ComboBox({
                        store: store,
                        valueField: 'id',
                        displayField: 'name',
                        queryMode: 'local',
                        required: true
                    }),
                    renderer: function (shippingRule) {
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
                    header: t('coreshop_carriers_stop_propagation'),
                    dataIndex: 'stopPropagation',
                    flex: 1,
                    xtype: 'checkcolumn',
                    listeners: {
                        checkchange: function (column, rowIndex, checked, eOpts) {
                            var grid = column.up('grid'),
                                store = grid.getStore();
                            if (checked) {
                                store.each(function (record, index) {
                                    if (rowIndex !== index) {
                                        record.set('stopPropagation', false);
                                    }
                                });
                            }
                        }
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
                            stopPropagation: false,
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
                stopPropagation: group.get('stopPropagation'),
                shippingRule: group.get('shippingRule'),
                carrier: this.data.id
            };

            data.shippingRules.push(rule);
        }.bind(this));

        return data;
    }
});
