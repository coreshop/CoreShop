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

pimcore.registerNS('coreshop.order.sale.create.step.base');
coreshop.order.sale.create.step.base = Class.create(coreshop.order.sale.create.abstractStep, {
    isValid: function () {
        var values = this.getValues();

        return values.currency && values.language;
    },

    getPriority: function () {
        return 20;
    },

    reset: function() {
        this.panel.getForm().reset();
    },

    setPreviewData: function(data) {

    },

    getValues: function (parent) {
        return this.panel.getForm().getFieldValues();
    },

    getPanel: function () {
        this.panel = Ext.create('Ext.form.Panel', {
            items: this.getBaseItems()
        });

        return this.panel;
    },

    getBaseItems: function () {
        var languageStore = [];
        var websiteLanguages = pimcore.settings.websiteLanguages;

        for (var i = 0; i < websiteLanguages.length; i++) {
            languageStore.push([websiteLanguages[i], pimcore.available_languages[websiteLanguages[i]] + " [" + websiteLanguages[i] + "]"]);
        }

        var defaultStore = pimcore.globalmanager.get('coreshop_stores').findRecord('isDefault', true);

        if (!defaultStore) {
            defaultStore = pimcore.globalmanager.get('coreshop_stores').getAt(0);
        }

        return [
            Ext.create({
                xtype: 'coreshop.store',
                value: defaultStore.getId(),
                listeners: {
                    select: function () {
                        this.eventManager.fireEvent('preview');
                    }.bind(this)
                }
            }),
            {
                xtype: 'coreshop.currency',
                displayTpl: Ext.create('Ext.XTemplate', '<tpl for=".">', '{name} ({symbol})', '</tpl>'),
                listConfig: {
                    itemTpl: Ext.create('Ext.XTemplate', '', '{name} ({symbol})', '')
                },
                listeners: {
                    select: function () {
                        this.eventManager.fireEvent('preview');
                    }.bind(this)
                },
                value: defaultStore.get('currency')
            },
            new Ext.form.ComboBox({
                fieldLabel: t('language'),
                name: "language",
                store: languageStore,
                editable: false,
                triggerAction: 'all',
                mode: "local",
                width: 500,
                emptyText: t('language'),
                value: languageStore[0],
                listeners: {
                    select: function () {
                        this.eventManager.fireEvent('preview');
                    }.bind(this)
                }
            })
        ];
    },

    getName: function () {
        return t('coreshop_order_create_base');
    },

    getIconCls: function () {
        return 'coreshop_icon_localization';
    }
});
