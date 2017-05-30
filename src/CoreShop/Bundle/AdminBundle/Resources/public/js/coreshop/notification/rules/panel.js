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

pimcore.registerNS('pimcore.plugin.coreshop.notification.rules.panel');

pimcore.plugin.coreshop.notification.rules.panel = Class.create(pimcore.plugin.coreshop.rules.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_notification_rule_panel',
    storeId : 'coreshop_notification_rules',
    iconCls : 'coreshop_icon_notification_rule',
    type : 'notification_rule',

    url : {
        add : '/admin/coreshop/notification_rules/add',
        delete : '/admin/coreshop/notification_rules/delete',
        get : '/admin/coreshop/notification_rules/get',
        list : '/admin/coreshop/notification_rules/list',
        config : '/admin/coreshop/notification_rules/get-config',
        sort : '/admin/coreshop/notification_rules/sort'
    },

    getItemClass : function () {
        return pimcore.plugin.coreshop.notification.rules.item;
    },

    getActionsForType : function(allowedType) {
        var actions = this.getActions();

        if(actions.hasOwnProperty(allowedType)) {
            return actions[allowedType];
        }

        return [];
    },

    getConditionsForType : function(allowedType) {
        var conditions = this.getConditions();
        var allowedConditions = [];

        if(conditions.hasOwnProperty(allowedType)) {
            return conditions[allowedType];
        }

        return [];
    },

    getNavigation: function () {
        if (!this.grid) {

            this.grid = Ext.create('Ext.grid.Panel', {
                region: 'west',
                store: pimcore.globalmanager.get(this.storeId),
                columns: [
                    {
                        text: '',
                        dataIndex: 'name',
                        flex : 1,
                        renderer: function (value, metadata, record)
                        {
                            metadata.tdAttr = 'data-qtip="ID: ' + record.get("id") + '"';

                            return value;
                        }
                    }
                ],
                listeners : this.getTreeNodeListeners(),
                useArrows: true,
                autoScroll: true,
                animate: true,
                containerScroll: true,
                width: 200,
                split: true,
                tbar: {
                    items: [
                        {
                            // add button
                            text: t('add'),
                            iconCls: 'pimcore_icon_add',
                            handler: this.addItem.bind(this)
                        }
                    ]
                },
                bbar : {
                    items : ['->', {
                        iconCls: 'pimcore_icon_reload',
                        scale : 'small',
                        handler: function() {
                            this.grid.getStore().load();
                        }.bind(this)
                    }]
                },
                hideHeaders: true,
                viewConfig: {
                    plugins: {
                        ptype: 'gridviewdragdrop',
                        dragText: t('coreshop_grid_reorder')
                    },
                    listeners: {
                        drop: function(node, data, dropRec, dropPosition) {
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
                                }.bind(this)
                            });
                        }.bind(this)
                    }
                }
            });

            this.grid.on('beforerender', function () {
                this.getStore().load();
            });

        }

        return this.grid;
    }
});
