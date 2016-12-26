/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.mail.rules.actions.mail');

pimcore.plugin.coreshop.mail.rules.actions.mail = Class.create(pimcore.plugin.coreshop.rules.actions.abstract, {

    type : 'mail',

    getForm : function () {
        var me = this,
            tabs = [];

        if (this.data) {

        }

        Ext.each(pimcore.settings.websiteLanguages, function (lang) {
            var shortLang = lang.toLowerCase();

            if (shortLang.indexOf('-') > 0) {
                shortLang = shortLang.split('-');
                shortLang = shortLang[0];
            }

            tabs.push({
                title: pimcore.available_languages[lang],
                iconCls: 'pimcore_icon_language_' + lang.toLowerCase(),
                layout: 'form',
                items: [
                    {
                        name: 'document.' + shortLang,
                        value: '',
                        fieldLabel: t('coreshop_messaging_customer_email'),
                        labelWidth: 350,
                        fieldCls: 'pimcore_droptarget_input',
                        xtype: 'textfield',
                        listeners: me._getMailTemplateDropAreaListener()
                    }
                ]
            });

        });

        this.form = new Ext.form.FieldSet({
            items : [
                {
                    xtype: 'tabpanel',
                    activeTab: 0,
                    width: '100%',
                    defaults: {
                        autoHeight: true,
                        bodyStyle: 'padding:10px;'
                    },
                    items: tabs
                }
            ]
        });

        return this.form;
    },

    _getMailTemplateDropAreaListener: function() {

        return {

            render: function (el) {
                new Ext.dd.DropZone(el.getEl(), {
                    reference: this,
                    ddGroup: 'element',
                    getTargetFromEvent: function (e) {
                        return this.getEl();
                    }.bind(el),

                    onNodeOver: function (target, dd, e, data) {
                        data = data.records[0].data;
                        if (data.elementType === 'document' && data.type === 'email') {
                            return Ext.dd.DropZone.prototype.dropAllowed;
                        }

                        return Ext.dd.DropZone.prototype.dropNotAllowed;
                    },

                    onNodeDrop: function (target, dd, e, data) {
                        data = data.records[0].data;

                        if (data.elementType === 'document' && data.type === 'email') {
                            this.setValue(data.id);
                            return true;
                        }

                        return false;
                    }.bind(el)
                });
            }

        };
    }
});
