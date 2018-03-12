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

pimcore.registerNS('coreshop.notification.rule.panel');

coreshop.notification.rule.panel = Class.create(coreshop.rules.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_notification_rule_panel',
    storeId: 'coreshop_notification_rules',
    iconCls: 'coreshop_icon_notification_rule',
    type: 'coreshop_notification_rule',

    url: {
        add: '/admin/coreshop/notification_rules/add',
        delete: '/admin/coreshop/notification_rules/delete',
        get: '/admin/coreshop/notification_rules/get',
        list: '/admin/coreshop/notification_rules/list',
        config: '/admin/coreshop/notification_rules/get-config',
        sort: '/admin/coreshop/notification_rules/sort'
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
                            url: this.url.sort,
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
