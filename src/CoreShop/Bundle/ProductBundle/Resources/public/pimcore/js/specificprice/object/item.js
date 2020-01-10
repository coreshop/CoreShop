/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.product.specificprice.object');
pimcore.registerNS('coreshop.product.specificprice.object.item');
coreshop.product.specificprice.object.item = Class.create(coreshop.rules.item, {

    iconCls: 'coreshop_icon_price_rule',

    postSaveObject: function (object, refreshedRuleData, task, fieldName) {
        // remove dirty flag!
        //this.settingsForm.getForm().setValues(this.settingsForm.getForm().getValues());
    },

    getPanel: function () {
        this.panel = new Ext.TabPanel({
            activeTab: 0,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            iconCls: this.iconCls,
            items: this.getItems(),
            tabConfig: {
                html: this.generatePanelTitle(this.data.name, this.data.active)
            }
        });

        return this.panel;
    },

    generatePanelTitle: function (title, active) {
        var data = [title];
        if (active === false) {
            data.push('<span class="pimcore_rule_disabled standalone"></span>')
        }

        return data.join(' ');
    },

    initPanel: function () {
        this.panel = this.getPanel();
        this.parentPanel.getTabPanel().add(this.panel);
    },

    getActionContainerClass: function () {
        return coreshop.product.specificprice.action;
    },

    getConditionContainerClass: function () {
        return coreshop.product.specificprice.condition;
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
                value: data.name,
                enableKeyEvents: true,
                listeners: {
                    keyup: function (field) {
                        var activeField = field.up('form').getForm().findField('active');
                        this.panel.setTitle(this.generatePanelTitle(field.getValue(), activeField.getValue()));
                    }.bind(this)
                }
            }, {
                xtype: 'numberfield',
                name: 'priority',
                fieldLabel: t('coreshop_priority'),
                value: this.data.priority ? this.data.priority : 0,
                width: 250
            }, {
                xtype: 'checkbox',
                name: 'inherit',
                fieldLabel: t('coreshop_inherit'),
                hidden: true, // currently not implemented
                checked: this.data.inherit == '1'
            }, {
                xtype: 'checkbox',
                name: 'active',
                fieldLabel: t('active'),
                checked: this.data.active,
                listeners: {
                    change: function (field, state) {
                        var nameField = field.up('form').getForm().findField('name');
                        this.panel.setTitle(this.generatePanelTitle(nameField.getValue(), field.getValue()));
                    }.bind(this)
                }
            }, {
                xtype: 'checkbox',
                name: 'stopPropagation',
                fieldLabel: t('coreshop_stop_propagation'),
                checked: this.data.stopPropagation
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
        saveData['conditions'] = this.conditions.getConditionsData();
        saveData['actions'] = this.actions.getActionsData();

        if (this.data.id) {
            saveData['id'] = this.data.id;
        }

        return saveData;

    },

    getId: function () {
        return this.data.id ? this.data.id : null;
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
