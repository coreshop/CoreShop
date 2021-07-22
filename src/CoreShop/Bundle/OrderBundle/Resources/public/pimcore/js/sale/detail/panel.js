/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.sale.detail.panel');
coreshop.order.sale.detail.panel = Class.create({
    modules: {},
    eventManager: null,
    blocks: {},

    sale: null,
    objectData: null,
    layoutId: null,
    type: 'sale',
    iconCls: '',

    borderStyle: {
        borderStyle: 'solid',
        borderColor: '#ccc',
        borderRadius: '5px',
        borderWidth: '1px'
    },

    initialize: function (sale) {
        var me = this;

        me.blocks = {};
        me.modules = {};
        me.eventManager = new CoreShop.resource.EventManager();
        me.sale = sale;
        me.layoutId = 'coreshop_' + this.type + '_' + this.sale.o_id;
        me.iconCls = 'coreshop_icon_' + this.type;
        me.getLayout();
    },

    activate: function () {
        var tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.setActiveItem(this.layoutId);
    },

    reload: function () {
        var me = this;

        me.layout.setLoading(t('loading'));

        Ext.Ajax.request({
            url: '/admin/coreshop/'+me.type+'/detail',
            params: {
                id: me.sale.o_id
            },
            success: function (response) {
                var res = Ext.decode(response.responseText);

                if (res.success) {
                    me.updateSale(res.sale);
                } else {
                    Ext.Msg.alert(t('open_target'), t('problem_opening_new_target'));
                }

                me.layout.setLoading(false);
            }.bind(this)
        });
    },

    updateSale: function(sale) {
        var me = this;

        me.sale = sale;
        Ext.Object.each(me.blocks, function(id, block) {
            block.setSale(sale);
        });
    },

    getTopButtons: function () {
        var me = this,
            buttons = [];

        Ext.Object.each(me.blocks, function(id, block) {
            buttons.push.apply(buttons, block.getTopBarItems());
        });

        return buttons;
    },

    getLayout: function () {
        if (!this.layout) {

            var buttons = [{
                iconCls: 'pimcore_icon_reload',
                text: t('reload'),
                handler: function () {
                    this.reload();
                }.bind(this)
            }];
            var items = this.getItems();
            buttons = buttons.concat(this.getTopButtons());

            // create new panel
            this.layout = new Ext.panel.Panel({
                id: this.layoutId,
                title: t('coreshop_' + this.type) + ': ' + this.sale.saleNumber,
                iconCls: this.iconCls,
                border: false,
                layout: 'border',
                scrollable: 'y',
                closable: true,
                items: items,
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: buttons
                }]
            });

            // add event listener
            this.layout.on('destroy', function () {
                pimcore.globalmanager.remove(this.layoutId);
            }.bind(this));

            // add panel to pimcore panel tabs
            var tabPanel = Ext.getCmp('pimcore_panel_tabs');
            tabPanel.add(this.layout);
            tabPanel.setActiveItem(this.layoutId);

            // update layout
            pimcore.layout.refresh();
        }

        return this.layout;
    },

    getItems: function () {
        return [this.getPanel()];
    },

    getBlockIdentifier: function () {
        return coreshop.order.sale.detail.blocks;
    },

    getPanel: function () {
        var me = this,
            defaults = {
                style: this.borderStyle,
                cls: 'coreshop-panel',
                bodyPadding: 5
            },
            blockIdentifier = me.getBlockIdentifier(),
            blockKeys = Object.keys(blockIdentifier),
            blocks = [],
            leftItems = [],
            topItems = [],
            rightItems = [],
            bottomItems = [];

        blockKeys.forEach(function (blockName) {
            var block = new blockIdentifier[blockName](me, me.eventManager);

            blocks.push(block);
            me.blocks[blockName] = block;
        });

        blocks = blocks.sort(function (blockA, blockB) {
            var blockAPriority = blockA.getPriority();
            var blockBPriority = blockB.getPriority();

            if (blockAPriority > blockBPriority) {
                return 1;
            }
            if (blockAPriority < blockBPriority) {
                return -1;
            }

            return 0;
        });

        blocks.forEach(function (block) {
            var position = block.getPosition();
            var layout = block.getLayout();

            if (layout === false || layout === null) {
                return false;
            }

            switch (position) {
                case 'top':
                    layout.setMargin('0 0 20 0');
                    topItems.push(layout);
                    break;
                case 'left':
                    layout.setMargin('0 20 20 0');
                    leftItems.push(layout);
                    break;
                case 'bottom':
                    layout.setMargin('0 0 20 0');
                    bottomItems.push(layout);
                    break;
                case 'right':
                    layout.setMargin('0 0 20 0');
                    rightItems.push(layout);
                    break;
            }
        });

        var contentItems = [
            {
                xtype: 'container',
                border: 0,
                style: {
                    border: 0
                },
                flex: 7,
                defaults: defaults,
                items: leftItems
            },
            {
                xtype: 'container',
                border: 0,
                style: {
                    border: 0
                },
                flex: 5,
                defaults: defaults,
                items: rightItems
            }
        ];
        topItems.push(
            {
                xtype: 'container',
                layout: 'hbox',
                margin: '0 0 20 0',
                border: 0,
                style: {
                    border: 0
                },
                items: contentItems
            }
        );

        topItems.push.apply(topItems, bottomItems);

        this.panel = Ext.create('Ext.container.Container', {
            border: false,
            items: topItems,
            padding: 20,
            region: 'center',
            defaults: defaults
        });

        return this.panel;
    }
});
