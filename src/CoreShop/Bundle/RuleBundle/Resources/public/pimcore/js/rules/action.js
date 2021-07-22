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

pimcore.registerNS('coreshop.rules.action');
coreshop.rules.action = Class.create({
    dirty: false,

    initialize: function (actions) {
        this.actions = actions;
        this.dirty = false;
    },

    reload: function (actions) {
        this.actionsContainer.removeAll();

        Ext.each(actions, function(action) {
            this.addAction(action.type, action, false);
        }.bind(this));
    },

    getLayout: function () {
        // init
        var _this = this;
        var addMenu = [];

        // show only defined actions
        Ext.each(this.actions, function (action) {

            if (action === 'abstract')
                return;

            addMenu.push({
                iconCls: 'coreshop_rule_icon_action_' + action,
                text: t('coreshop_action_' + action),
                handler: _this.addAction.bind(_this, action, null, false)
            });
        });

        this.actionsContainer = new Ext.Panel({
            iconCls: 'coreshop_rule_actions',
            title: t('actions'),
            autoScroll: true,
            forceLayout: true,
            style: 'padding: 10px',
            tbar: [{
                iconCls: 'pimcore_icon_add',
                menu: addMenu
            }],
            border: false
        });

        return this.actionsContainer;
    },

    setDirty: function(dirty) {
        this.dirty = dirty;
    },

    destroy: function () {
        if (this.actionsContainer) {
            this.actionsContainer.destroy();
        }
    },

    addAction: function (type, data, dirty) {
        var actionClass = this.getActionClassItem(type);
        var item = new actionClass(this, type, data);

        this.actionsContainer.add(item.getLayout());
        this.actionsContainer.updateLayout();

        if (dirty) {
            this.setDirty(true);
        }
    },

    getActionClassItem: function (type) {
        if (Object.keys(this.getActionClassNamespace()).indexOf(type) >= 0) {
            return this.getActionClassNamespace()[type];
        }

        return this.getDefaultActionClassItem();
    },

    getActionClassNamespace: function () {
        return coreshop.rules.actions;
    },

    getDefaultActionClassItem: function () {
        return coreshop.rules.actions.abstract;
    },

    getActionsData: function () {
        // get defined actions
        var actionData = [];
        var actions = this.actionsContainer.items.getRange();
        for (var i = 0; i < actions.length; i++) {
            var action = {};
            var configuration = {};

            var actionItem = actions[i];
            var actionClass = actionItem.xparent;

            if (Ext.isFunction(actionClass['getValues'])) {
                configuration = actionClass.getValues();
            } else {
                var form = actionClass.form;

                if (form) {
                    if (Ext.isFunction(form.getValues)) {
                        configuration = form.getValues();
                    }
                    else {
                        for (var c = 0; c < form.items.length; c++) {
                            var item = form.items.get(c);

                            try {
                                configuration[item.getName()] = item.getValue();
                            }
                            catch (e) {

                            }

                        }
                    }
                }
            }

            if (actionClass.id) {
                action['id'] = actionClass.id;
            }

            action['configuration'] = configuration;
            action['type'] = actions[i].xparent.type;
            action['sort'] = (i + 1);

            actionData.push(action);

            if (Ext.isFunction(this.prepareAction)) {
                action = this.prepareAction(action);
            }
        }

        return actionData;
    },

    isDirty: function () {
        if (this.dirty) {
            return true;
        }

        if (this.actionsContainer.items) {
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
