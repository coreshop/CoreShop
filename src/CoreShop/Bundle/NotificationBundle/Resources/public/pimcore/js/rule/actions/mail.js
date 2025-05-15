/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.notification.rule.actions.mail');

coreshop.notification.rule.actions.mail = Class.create(coreshop.rules.actions.abstract, {
    type: 'mail',
    fields: {},

    initialize: function ($super, parent, type, data) {
        $super(parent, type, data);

        this.fields = {};
    },

    getForm: function () {
        var me = this,
            tabs = [];

        Ext.each(pimcore.settings.websiteLanguages, function (lang) {
            var value = me.data && me.data.mails && me.data.mails.hasOwnProperty(lang) ? me.data.mails[lang] : '';

            var elementHref = new coreshop.object.elementHref({
                id: value,
                type: 'document',
                subtype: 'email'
            }, {
                classes: [],
                documentsAllowed: true,
                documentTypes: [{
                    documentTypes: 'email'
                }],
                name: 'mails[' + lang + ']',
                title: t('coreshop_email_document')
            });

            tabs.push({
                title: pimcore.available_languages[lang],
                iconCls: 'pimcore_icon_language_' + lang.toLowerCase(),
                layout: 'form',
                items: [
                    elementHref.getLayoutEdit()
                ]
            });

            me.fields[lang] = elementHref;
        });

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

        Ext.Object.each(this.fields, function (key, elementHref) {
            values[key] = elementHref.data.id;
        });

        return {
            mails: values,
            doNotSendToDesignatedRecipient: this.doNotSendToDesignatedRecipient.getValue()
        };
    }
});
