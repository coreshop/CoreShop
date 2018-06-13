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

pimcore.registerNS('coreshop.provider.item');
coreshop.provider.item = Class.create(coreshop.resource.item, {

    iconCls: 'coreshop_icon_payment_provider',

    url: {
        save: '/admin/coreshop/payment_providers/save'
    },

    getItems: function () {
        return [this.getFormPanel()];
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
            forceLayout: true,
            defaults: {
                forceLayout: true
            },
            buttons: [
                {
                    text: t('save'),
                    handler: this.save.bind(this, function(res) {
                        if (res.success) {
                            this.formPanel.down('#paymentFactory').setReadOnly(true);
                        }
                    }.bind(this)),
                    iconCls: 'pimcore_icon_apply'
                }
            ],
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
    },

    getSaveData: function () {
        var values = this.formPanel.getForm().getFieldValues();

        if (!values['active']) {
            delete values['active'];
        }

        return values;
    }
});
