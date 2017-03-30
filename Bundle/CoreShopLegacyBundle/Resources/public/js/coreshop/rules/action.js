/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.rules.action');

pimcore.plugin.coreshop.rules.action = Class.create({

    initialize : function (actions) {
        this.actions = actions;
    },

    getLayout: function () {
        // init
        var _this = this;
        var addMenu = [];

        // show only defined actions
        Ext.each(this.actions, function (action) {

            if (action == 'abstract')
                return;

            addMenu.push({
                iconCls: 'coreshop_rule_icon_action_' + action,
                text: t('coreshop_action_' + action),
                handler: _this.addAction.bind(_this, action, null)
            });
        });

        this.actionsContainer = new Ext.Panel({
            iconCls: 'coreshop_rule_actions',
            title: t('actions'),
            autoScroll: true,
            forceLayout: true,
            style : 'padding: 10px',
            tbar: [{
                iconCls: 'pimcore_icon_add',
                menu: addMenu
            }],
            border: false
        });

        return this.actionsContainer;
    },

    destroy : function() {
        if(this.actionsContainer) {
            this.actionsContainer.destroy();
        }
    },

    addAction: function (type, data) {
        var actionClass = this.getActionClassItem(type);
        var item = new actionClass(this, type, data);

        this.actionsContainer.add(item.getLayout());
        this.actionsContainer.updateLayout();
    },

    getActionClassItem : function (type) {
        if(Object.keys(this.getActionClassNamespace()).indexOf(type) >= 0) {
            return this.getActionClassNamespace()[type];
        }

        return this.getDefaultActionClassItem();
    },

    getActionClassNamespace : function() {
        return pimcore.plugin.coreshop.rules.actions;
    },

    getDefaultActionClassItem : function() {
        return pimcore.plugin.coreshop.rules.actions.abstract;
    },

    getActionsData : function () {
        // get defined actions
        var actionData = [];
        var actions = this.actionsContainer.items.getRange();
        for (var i = 0; i < actions.length; i++)
        {
            var action = {};

            var actionItem = actions[i];
            var actionClass = actionItem.xparent;
            var form = actionClass.form;

            if (Ext.isFunction(actionClass['getValues'])) {
                action = actionClass.getValues();
            } else {
                if (Ext.isFunction(form.getValues)) {
                    action = form.getValues();
                }
                else {
                    for (var c = 0; c < form.items.length; c++) {
                        var item = form.items.get(c);

                        try {
                            action[item.getName()] = item.getValue();
                        }
                        catch (e) {

                        }

                    }
                }
            }

            action['type'] = actions[i].xparent.type;
            actionData.push(action);
        }

        return actionData;
    },

    isDirty : function() {
        if(this.actionsContainer.items) {
            var actions = this.actionsContainer.items.getRange();
            for (var i = 0; i < actions.length; i++) {
                var actionsItem = actions[i];
                var actionsClass = actionsItem.xparent;

                if (Ext.isFunction(actionsClass['isDirty'])) {
                    if (actionsClass.isDirty()) {
                        return true;
                    }
                } else {
                    var form = actionsClass.form;

                    if (form) {
                        if (form.isDirty()) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
});
