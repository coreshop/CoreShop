/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.provider.item');
coreshop.provider.item = Class.create(coreshop.resource.item, {

    iconCls: 'coreshop_icon_payment_provider',

    routing: {
        save: 'coreshop_payment_provider_save'
    },

    getPanel: function () {
        return new Ext.TabPanel({
            activeTab: 0,
            title: this.getTitleText(),
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
            this.getFormPanel(),
            this.getPaymentLocationsAndCosts()

        ];
    },

    getTitleText: function () {
        return this.data.identifier;
    },

    getFormPanel: function () {
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
                    width: 400,
                    value: data.translations[lang] ? data.translations[lang].title : ''
                }, {
                    xtype: 'textarea',
                    name: 'translations.' + lang + '.description',
                    fieldLabel: t('description'),
                    width: 400,
                    value: data.translations[lang] ? data.translations[lang].description : ''
                }, {
                    xtype: 'textarea',
                    name: 'translations.' + lang + '.instructions',
                    fieldLabel: t('coreshop_instructions'),
                    width: 400,
                    value: data.translations[lang] ? data.translations[lang].instructions : ''
                }]
            };

            langTabs.push(tab);
        });

        var items = [
            {
                fieldLabel: t('coreshop_identifier'),
                name: 'identifier',
                value: this.data.identifier
            },
            {
                fieldLabel: t('coreshop_position'),
                name: 'position',
                value: this.data.position
            },
            {
                xtype: 'checkbox',
                fieldLabel: t('active'),
                name: 'active',
                checked: this.data.active
            },
            this.getLogoSelect().getLayoutEdit(),
            {
                xtype: 'combobox',
                itemId: 'paymentFactory',
                fieldLabel: t('coreshop_payment_provider_factory'),
                name: 'gatewayConfig.factoryName',
                length: 255,
                value: this.data.gatewayConfig ? this.data.gatewayConfig.factoryName : '',
                store: pimcore.globalmanager.get('coreshop_payment_provider_factories'),
                valueField: 'type',
                displayField: 'name',
                queryMode: 'local',
                readOnly: this.data.gatewayConfig && this.data.gatewayConfig.factoryName ? true : false,
                listeners: {
                    change: function (combo, newValue) {
                        this.getGatewayConfigPanel().removeAll();

                        this.getGatewayConfigPanelLayout(newValue);
                    }.bind(this)
                }
            },
            {
                xtype: 'tabpanel',
                activeTab: 0,
                defaults: {
                    autoHeight: true,
                    bodyStyle: 'padding:10px;'
                },
                items: langTabs
            }
        ];

        this.formPanel = new Ext.form.Panel({
            bodyStyle: 'padding:20px 5px 20px 5px;',
            border: false,
            region: 'center',
            autoScroll: true,
            title: t('coreshop_payment_provider_main_panel'),
            forceLayout: true,
            defaults: {
                forceLayout: true
            },
            items: [
                {
                    xtype: 'fieldset',
                    autoHeight: true,
                    labelWidth: 350,
                    defaultType: 'textfield',
                    defaults: {width: '100%'},
                    items: items
                },
                this.getGatewayConfigPanel()
            ]
        });

        if (this.data.gatewayConfig && this.data.gatewayConfig.factoryName) {
            this.getGatewayConfigPanelLayout(this.data.gatewayConfig.factoryName);
        }

        return this.formPanel;
    },

    getGatewayConfigPanel: function () {
        if (!this.gatewayConfigPanel) {
            this.gatewayConfigPanel = new Ext.form.FieldSet({
                defaults: {anchor: '90%'}
            });
        }

        return this.gatewayConfigPanel;
    },

    getPaymentProviderRulesGrid: function () {
        this.paymentProviderRuleGroupsStore = new Ext.data.Store({
            restful: false,
            idProperty: 'id',
            sorters: 'priority',
            data: this.data.paymentProviderRules
        });

        var store = Ext.create('store.coreshop_payment_provider_rules');
        store.load(function() {
            this.paymentProviderRuleGroupsGrid.setStore(this.paymentProviderRuleGroupsStore);
        }.bind(this));

        this.paymentProviderRuleGroupsGrid = Ext.create('Ext.grid.Panel', {
            columns: [
                {
                    header: t('coreshop_carriers_payment_provider_rule'),
                    flex: 2,
                    dataIndex: 'paymentProviderRule',
                    editor: new Ext.form.ComboBox({
                        store: store,
                        valueField: 'id',
                        displayField: 'name',
                        queryMode: 'local',
                        required: true
                    }),
                    renderer: function (paymentProviderRule) {
                        var pos = store.findExact('id', paymentProviderRule);
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
                        this.paymentProviderRuleGroupsStore.add({
                            id: null,
                            carrier: this.data.id,
                            paymentProviderRule: null,
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

        return this.paymentProviderRuleGroupsGrid;
    },

    getPaymentLocationsAndCosts: function () {
        //Payment locations and costs
        this.paymentProviderRules = new Ext.form.Panel({
            iconCls: 'coreshop_carrier_costs_icon',
            title: t('coreshop_payment_provider_rule'),
            bodyStyle: 'padding:10px;',
            autoScroll: true,
            border: false,
            items: [this.getPaymentProviderRulesGrid()]
        });

        return this.paymentProviderRules;
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

    getSaveData: function () {
        var data = {
            paymentProviderRules: []
        };

        Ext.apply(data, this.formPanel.getForm().getFieldValues());

        var ruleGroups = this.paymentProviderRuleGroupsStore.getRange();

        Ext.each(ruleGroups, function (group) {
            var rule = {
                priority: group.get('priority'),
                stopPropagation: group.get('stopPropagation'),
                paymentProviderRule: group.get('paymentProviderRule'),
                paymentProvider: this.data.id
            };

            data.paymentProviderRules.push(rule);
        }.bind(this));

        return data;
    },

    getGatewayConfigPanelLayout: function (type) {
        if (type) {
            type = type.toLowerCase();

            //Check if some class for getterPanel is available
            if (coreshop.provider.gateways[type]) {
                var getter = new coreshop.provider.gateways[type];

                this.getGatewayConfigPanel().add(getter.getLayout(this.data.gatewayConfig ? this.data.gatewayConfig.config : []));
                this.getGatewayConfigPanel().show();
            } else {
                this.getGatewayConfigPanel().hide();
            }
        } else {
            this.getGatewayConfigPanel().hide();
        }
    }
});
