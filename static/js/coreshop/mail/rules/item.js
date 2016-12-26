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

pimcore.registerNS('pimcore.plugin.coreshop.mail.rules.item');

pimcore.plugin.coreshop.mail.rules.item = Class.create(pimcore.plugin.coreshop.rules.item, {

    iconCls : 'coreshop_icon_mail_rule',

    url : {
        save : '/plugin/CoreShop/admin_mail-rule/save'
    },

    getPanel: function () {
        var items = this.getItems();

        this.panel = new Ext.TabPanel({
            activeTab: 0,
            title: this.data.name,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            iconCls : this.iconCls,
            buttons: [{
                text: t('save'),
                iconCls: 'pimcore_icon_apply',
                handler: this.save.bind(this)
            }],
            items: items
        });

        if(this.data.mailType) {
            this.reloadTypes(this.data.mailType);
        }

        return this.panel;
    },

    getSettings: function () {
        var data = this.data;
        var types = [];

        this.parentPanel.getConfig().types.forEach(function (type) {
            types.push([type]);
        }.bind(this));

        var typesStore = new Ext.data.ArrayStore({
            data : types,
            fields: ['type'],
            idProperty : 'type'
        });

        this.settingsForm = Ext.create('Ext.form.Panel', {
            iconCls: 'coreshop_icon_settings',
            title: t('settings'),
            bodyStyle: 'padding:10px;',
            autoScroll: true,
            border:false,
            items: [
                {
                    xtype: 'textfield',
                    name: 'name',
                    fieldLabel: t('name'),
                    width: 250,
                    value: data.name
                },
                {
                    xtype : 'combo',
                    fieldLabel: t('coreshop_mail_rule_type'),
                    name: 'mailType',
                    displayField: 'type',
                    valueField: 'type',
                    store: typesStore,
                    value : this.data.mailType,
                    width: 250,
                    listeners : {
                        change : function (combo, value) {
                            this.reloadTypes(value);
                        }.bind(this)
                    }
                }
            ]
        });

        return this.settingsForm;
    },

    getItems : function () {
        return [
            this.getSettings()
        ];
    },

    reloadTypes : function(type) {
        if(this.actions) {
            this.actions.destroy();
        }

        if(this.conditions) {
            this.conditions.destroy();
        }

        var items = this.getItemsForType(type);

        this.panel.add(items);
    },

    getItemsForType : function(type) {
        var actionContainerClass = this.getActionContainerClass();
        var conditionContainerClass = this.getConditionContainerClass();

        var allowedActions = this.parentPanel.getActionsForType(type);
        var allowedConditions = this.parentPanel.getConditionsForType(type);

        this.actions = new actionContainerClass(allowedActions);
        this.conditions = new conditionContainerClass(allowedConditions);

        var items = [
            this.conditions.getLayout(),
            this.actions.getLayout()
        ];

        // add saved conditions
        if (this.data.conditions)
        {
            Ext.each(this.data.conditions, function (condition) {
                if(allowedConditions.indexOf(condition.type) >= 0) {
                    this.conditions.addCondition(condition.type, condition);
                }
            }.bind(this));
        }

        // add saved actions
        if (this.data.actions)
        {
            Ext.each(this.data.actions, function (action) {
                if(allowedActions.indexOf(action.type) >= 0) {
                    this.actions.addAction(action.type, action);
                }
            }.bind(this));
        }

        return items;
    },

    getActionContainerClass : function () {
        return pimcore.plugin.coreshop.mail.action;
    },

    getConditionContainerClass : function () {
        return pimcore.plugin.coreshop.mail.condition;
    }
});
