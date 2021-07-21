/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.product.unit.item');
coreshop.product.unit.item = Class.create(coreshop.resource.item, {

    iconCls: 'coreshop_icon_product_units',

    routing: {
        save: 'coreshop_product_unit_save'
    },

    getPanel: function () {
        this.panel = new Ext.TabPanel({
            activeTab: 0,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            iconCls: this.iconCls,
            items: this.getItems(),
            buttons: [{
                text: t('save'),
                iconCls: 'pimcore_icon_apply',
                handler: this.save.bind(this)
            }],
            tabConfig: {
                html: this.data.name
            }
        });

        return this.panel;
    },

    getItems: function () {
        var data = this.data,
            langTabs = [];

        Ext.each(pimcore.settings.websiteLanguages, function (lang) {
            var tab = {
                title: pimcore.available_languages[lang],
                iconCls: 'pimcore_icon_language_' + lang.toLowerCase(),
                layout: 'form',
                items: [{
                    xtype: 'textfield',
                    name: 'translations.' + lang + '.fullLabel',
                    fieldLabel: t('coreshop_product_unit_full_label'),
                    width: 400,
                    value: data.translations[lang] ? data.translations[lang].fullLabel : ''
                },{
                    xtype: 'textfield',
                    name: 'translations.' + lang + '.fullPluralLabel',
                    fieldLabel: t('coreshop_product_unit_full_plural_label'),
                    width: 400,
                    value: data.translations[lang] ? data.translations[lang].fullPluralLabel : ''
                },{
                    xtype: 'textfield',
                    name: 'translations.' + lang + '.shortLabel',
                    fieldLabel: t('coreshop_product_unit_short_label'),
                    width: 400,
                    value: data.translations[lang] ? data.translations[lang].shortLabel : ''
                },{
                    xtype: 'textfield',
                    name: 'translations.' + lang + '.shortPluralLabel',
                    fieldLabel: t('coreshop_product_unit_short_plural_label'),
                    width: 400,
                    value: data.translations[lang] ? data.translations[lang].shortPluralLabel : ''
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
                fieldLabel: t('coreshop_product_unit_name'),
                width: 250,
                value: data.name,
                enableKeyEvents: true,
                listeners: {
                    keyup: function (field) {
                        this.panel.setTitle(field.getValue());
                    }.bind(this)
                }
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

    getSaveData: function () {

        var saveData;

        if (!this.settingsForm.getForm()) {
            return {};
        }

        saveData = this.settingsForm.getForm().getFieldValues();

        if (this.data.id) {
            saveData['id'] = this.data.id;
        }

        return saveData;

    },

    isDirty: function () {
        if (this.settingsForm.form.monitor && this.settingsForm.getForm().isDirty()) {
            return true;
        }

        if (this.conditions.isDirty()) {
            return true;
        }

        return !!this.actions.isDirty();
    }
});
