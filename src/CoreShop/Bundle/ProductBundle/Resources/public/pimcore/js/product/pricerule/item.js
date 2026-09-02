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

pimcore.registerNS('coreshop.product.pricerule.item');
coreshop.product.pricerule.item = Class.create(coreshop.rules.item, {

    iconCls: 'coreshop_icon_price_rule',

    routing: {
        save: 'coreshop_product_price_rule_save'
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
                    fieldLabel: t('coreshop_price_rule_label'),
                    width: 400,
                    value: data.translations && data.translations[lang] ? data.translations[lang].label : ''
                }]
            };

            langTabs.push(tab);
        });


        this.settingsForm = Ext.create('Ext.form.Panel', {
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
                value: data.name
            }, {
                xtype: 'numberfield',
                name: 'priority',
                fieldLabel: t('coreshop_priority'),
                value: this.data.priority ? this.data.priority : 0,
                width: 250
            }, {
                xtype: 'checkbox',
                name: 'stopPropagation',
                fieldLabel: t('coreshop_stop_propagation'),
                checked: this.data.stopPropagation
            }, {
                xtype: 'checkbox',
                name: 'active',
                fieldLabel: t('active'),
                checked: this.data.active
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

        return this.settingsForm;
    },

    getPanel: function () {
        this.panel = new Ext.TabPanel({
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

        return this.panel;
    },

    getActionContainerClass: function () {
        return coreshop.product.pricerule.action;
    },

    getConditionContainerClass: function () {
        return coreshop.product.pricerule.condition;
    }
});
