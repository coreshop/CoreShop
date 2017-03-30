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

pimcore.registerNS('pimcore.plugin.coreshop.mail.rules.panel');

pimcore.plugin.coreshop.mail.rules.panel = Class.create(pimcore.plugin.coreshop.rules.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_mail_rule_panel',
    storeId : 'coreshop_mail_rules',
    iconCls : 'coreshop_icon_mail_rule',
    type : 'mail_rule',

    url : {
        add : '/admin/CoreShop/mail-rule/add',
        delete : '/admin/CoreShop/mail-rule/delete',
        get : '/admin/CoreShop/mail-rule/get',
        list : '/admin/CoreShop/mail-rule/list',
        config : '/admin/CoreShop/mail-rule/get-config',
        sort : '/admin/CoreShop/mail-rule/sort'
    },

    getItemClass : function () {
        return pimcore.plugin.coreshop.mail.rules.item;
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
                        dataIndex: 'text',
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
