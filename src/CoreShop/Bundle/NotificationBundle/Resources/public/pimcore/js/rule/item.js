/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.notification.rule.item');

coreshop.notification.rule.item = Class.create(coreshop.rules.item, {

    iconCls: 'coreshop_icon_notification_rule',

    url: {
        save: '/admin/coreshop/notification_rules/save'
    },

    getPanel: function () {
        var items = this.getItems();

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
            items: items
        });

        if (this.data.type) {
            this.reloadTypes(this.data.type);
        }

        return this.panel;
    },

    getSettings: function () {
        var data = this.data;
        var types = [];

        this.parentPanel.getConfig().types.forEach(function (type) {
            types.push([type, t('coreshop_notification_rule_type_' + type)]);
        }.bind(this));

        var typesStore = new Ext.data.ArrayStore({
            data: types,
            fields: ['type', 'typeName'],
            idProperty: 'type'
        });

        this.settingsForm = Ext.create('Ext.form.Panel', {
            iconCls: 'coreshop_icon_settings',
            title: t('settings'),
            bodyStyle: 'padding:10px;',
            autoScroll: true,
            border: false,
            items: [
                {
                    xtype: 'textfield',
                    name: 'name',
                    fieldLabel: t('name'),
                    width: 250,
                    value: data.name
                },
                {
                    xtype: 'checkbox',
                    name: 'active',
                    fieldLabel: t('active'),
                    checked: data.active
                },
                {
                    xtype: 'combo',
                    fieldLabel: t('coreshop_notification_rule_type'),
                    name: 'type',
                    displayField: 'type',
                    valueField: 'type',
                    store: typesStore,
                    value: this.data.type,
                    width: 250,
                    listeners: {
                        change: function (combo, value) {
                            this.reloadTypes(value);
                        }.bind(this)
                    }
                }
            ]
        });

        return this.settingsForm;
    },

    getItems: function () {
        return [
            this.getSettings()
        ];
    },

    reloadTypes: function (type) {
        if (this.actions) {
            this.actions.destroy();
        }

        if (this.conditions) {
            this.conditions.destroy();
        }

        var items = this.getItemsForType(type);

        this.panel.add(items);
    },

    getItemsForType: function (type) {
        var actionContainerClass = this.getActionContainerClass();
        var conditionContainerClass = this.getConditionContainerClass();

        var allowedActions = this.parentPanel.getActionsForType(type);
        var allowedConditions = this.parentPanel.getConditionsForType(type);

        this.actions = new actionContainerClass(allowedActions, type);
        this.conditions = new conditionContainerClass(allowedConditions, type);

        var items = [
            this.conditions.getLayout(),
            this.actions.getLayout()
        ];

        // add saved conditions
        if (this.data.conditions) {
            Ext.each(this.data.conditions, function (condition) {
                var conditionType = condition.type.replace(type + '.', '');

                if (allowedConditions.indexOf(conditionType) >= 0) {
                    this.conditions.addCondition(conditionType, condition, false);
                }
            }.bind(this));
        }

        // add saved actions
        if (this.data.actions) {
            Ext.each(this.data.actions, function (action) {
                var actionType = action.type.replace(type + '.', '');

                if (allowedActions.indexOf(actionType) >= 0) {
                    this.actions.addAction(actionType, action, false);
                }
            }.bind(this));
        }

        return items;
    },

    getActionContainerClass: function () {
        return coreshop.notification.rule.action;
    },

    getConditionContainerClass: function () {
        return coreshop.notification.rule.condition;
    }
});
