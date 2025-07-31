/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.notification.rule.panel');

coreshop.notification.rule.panel = Class.create(coreshop.rules.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_notification_rule_panel',
    storeId: 'coreshop_notification_rules',
    iconCls: 'coreshop_icon_notification_rule',
    type: 'coreshop_notification_rule',
    permission: 'coreshop_permission_notification',

    routing: {
        add: 'coreshop_notification_rule_add',
        delete: 'coreshop_notification_rule_delete',
        get: 'coreshop_notification_rule_get',
        list: 'coreshop_notification_rule_list',
        config: 'coreshop_notification_rule_getConfig',
        sort: 'coreshop_notification_rule_sort'
    },

    getItemClass: function () {
        return coreshop.notification.rule.item;
    },

    getActionsForType: function (allowedType) {
        var actions = this.getActions();

        if (actions.hasOwnProperty(allowedType)) {
            return actions[allowedType];
        }

        return [];
    },

    getConditionsForType: function (allowedType) {
        var conditions = this.getConditions();

        if (conditions.hasOwnProperty(allowedType)) {
            return conditions[allowedType];
        }

        return [];
    },

    getGridConfiguration: function () {
        return {
            viewConfig: {
                plugins: {
                    ptype: 'gridviewdragdrop',
                    dragText: t('coreshop_grid_reorder')
                },
                listeners: {
                    drop: function (node, data, dropRec, dropPosition) {
                        this.grid.setLoading(t('loading'));

                        Ext.Ajax.request({
                            url: Routing.generate(this.routing.sort),
                            method: 'post',
                            params: {
                                rule: data.records[0].getId(),
                                toRule: dropRec.getId(),
                                position: dropPosition
                            },
                            callback: function (request, success, response) {
                                this.grid.setLoading(false);
                                this.grid.getStore().load();
                            }.bind(this)
                        });
                    }.bind(this)
                }
            }
        };
    },

    getItemClass: function() {
        return coreshop.notification.rule.item;
    }
});
