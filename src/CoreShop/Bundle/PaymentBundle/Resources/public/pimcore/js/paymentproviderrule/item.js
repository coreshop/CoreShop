/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.paymentproviderrule.item');

coreshop.paymentproviderrule.item = Class.create(coreshop.rules.item, {

    iconCls: 'coreshop_nav_icon_payment_provider_rule',

    routing: {
        save: 'coreshop_payment_provider_rule_save'
    },

    getPanel: function () {
        var items = this.getItems();
        const buttons = [];

        if (this.isAllowed('edit')) {
            buttons.push({
                text: t('save'),
                iconCls: 'pimcore_icon_save',
                handler: this.save.bind(this)
            });
        }

        this.panel = new Ext.TabPanel({
            activeTab: 0,
            title: this.data.name,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            iconCls: this.iconCls,
            buttons: buttons,
            items: items
        });

        return this.panel;
    },

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
                    name: 'translations.' + lang + '.label',
                    fieldLabel: t('coreshop_payment_provider_rule_label'),
                    width: 400,
                    value: data.translations && data.translations[lang] ? data.translations[lang].label : ''
                }]
            };

            langTabs.push(tab);
        });

        this.settingsForm = Ext.create('Ext.form.Panel', {
            disabled: !this.isAllowed('edit'),
            bodyStyle: 'padding:10px;',
            autoScroll: true,
            border: false,
            items: [{
                xtype: 'textfield',
                name: 'name',
                fieldLabel: t('name'),
                width: 250,
                value: data.name
            }, {
                xtype: 'checkbox',
                name: 'active',
                fieldLabel: t('active'),
                checked: data.active
            }, {
                xtype: 'tabpanel',
                activeTab: 0,
                defaults: {
                    autoHeight: true,
                    bodyStyle: 'padding:10px;'
                },
                width: '100%',
                items: langTabs
            }]
        });

        return new Ext.Panel({
            iconCls: 'coreshop_icon_settings',
            title: t('settings'),
            autoScroll: true,
            border: false,
            items: [this.settingsForm]
        });
    },


    getActionContainerClass: function () {
        return coreshop.paymentproviderrule.action;
    },

    getConditionContainerClass: function () {
        return coreshop.paymentproviderrule.condition;
    }
});
