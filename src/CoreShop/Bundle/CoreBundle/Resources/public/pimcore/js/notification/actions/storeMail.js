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

pimcore.registerNS('coreshop.notification.rule.actions.storeMail');

coreshop.notification.rule.actions.storeMail = Class.create(coreshop.notification.rule.actions.mail, {
    type: 'storeMail',

    fields: {},

    getForm: function () {
        var me = this,
            tabs = [];

        Ext.each(pimcore.globalmanager.get('coreshop_stores').getRange(), function (storeRecord) {
            var storeTabs = [];
            var storeValues = this.data && this.data.mails && this.data.mails.hasOwnProperty(storeRecord.getId()) ? this.data.mails[storeRecord.getId()] : {};

            this.fields[storeRecord.getId()] = {};

            Ext.each(pimcore.settings.websiteLanguages, function (lang) {
                var value = storeValues.hasOwnProperty(lang) ? storeValues[lang] : '';

                this.fields[storeRecord.getId()][lang] = new coreshop.object.elementHref({
                    id: value,
                    type: 'document',
                    subtype: 'email'
                }, {
                    documentsAllowed: true,
                    documentTypes: [{
                        documentTypes: 'email'
                    }],
                    name: 'mails[' + storeRecord.getId() + '][' + lang + ']',
                    title: t('coreshop_email_document')
                });

                storeTabs.push({
                    title: pimcore.available_languages[lang],
                    iconCls: 'pimcore_icon_language_' + lang.toLowerCase(),
                    layout: 'form',
                    items: [
                        this.fields[storeRecord.getId()][lang].getLayoutEdit()
                    ]
                });

            }.bind(this));

            tabs.push(
                {
                    xtype: 'tabpanel',
                    title: storeRecord.get('name'),
                    iconCls: 'coreshop_icon_store',
                    activeTab: 0,
                    width: '100%',
                    defaults: {
                        autoHeight: true,
                        bodyStyle: 'padding:10px;'
                    },
                    items: storeTabs
                }
            );
        }.bind(this));

        this.doNotSendToDesignatedRecipient = Ext.create({
            fieldLabel: t('coreshop_mail_rule_do_not_send_to_designated_recipient'),
            xtype: 'checkbox',
            name: 'doNotSendToDesignatedRecipient',
            checked: this.data ? this.data.doNotSendToDesignatedRecipient : false
        });

        this.form = new Ext.form.FieldSet({
            items: [
                {
                    xtype: 'tabpanel',
                    activeTab: 0,
                    width: '100%',
                    defaults: {
                        autoHeight: true,
                        bodyStyle: 'padding:10px;'
                    },
                    items: tabs
                },
                this.doNotSendToDesignatedRecipient
            ],
            getValues: this.getValues.bind(this)
        });

        return this.form;
    },

    getValues: function () {
        var values = {};

        Ext.Object.each(this.fields, function (storeId, storeElements) {
            values[storeId] = {};

            Ext.Object.each(storeElements, function(key, elementHref) {
                values[storeId][key] = elementHref.getValue();
            });
        });

        return {
            mails: values,
            doNotSendToDesignatedRecipient: this.doNotSendToDesignatedRecipient.getValue()
        };
    }
});
