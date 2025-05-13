/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.order.order.detail.panel');
coreshop.order.order.detail.panel = Class.create({
    modules: {},
    eventManager: null,
    blocks: {},
    previewMode: false,

    sale: null,
    objectData: null,
    layoutId: null,
    type: 'order',
    iconCls: '',
    previewButton: null,
    saveButton: null,

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
        me.layoutId = 'coreshop_' + this.type + '_' + this.sale.id;
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
            url: Routing.generate('coreshop_admin_order_get_order'),
            params: {
                id: me.sale.id
            },
            success: function (response) {
                var res = Ext.decode(response.responseText);

                if (res.success) {
                    me.updateSale(res.sale);
                } else {
                    Ext.Msg.alert(t('error'), t('error'));
                }

                me.setPreviewMode(false);

                me.layout.setLoading(false);
            }.bind(this)
        });
    },

    save: function () {
        var me = this;

        me.layout.setLoading(t('loading'));

        var data = {};

        Ext.Object.each(me.blocks, function(id, block) {
            data = Ext.apply({}, data, block.getUpdateValues());
        });

        Ext.Ajax.request({
            url: Routing.generate('coreshop_admin_order_update', {id: me.sale.id}),
            jsonData: data,
            method: 'post',
            success: function (response) {
                var res = Ext.decode(response.responseText);

                if (res.success) {
                    me.updateSale(res.sale);
                }

                me.setPreviewMode(false);

                me.layout.setLoading(false);
            }.bind(this)
        });
    },

    preview: function () {
        var me = this;

        me.layout.setLoading(t('loading'));

        var data = {};

        Ext.Object.each(me.blocks, function(id, block) {
            data = Ext.apply({}, data, block.getUpdateValues());
        });

        Ext.Ajax.request({
            url: Routing.generate('coreshop_admin_order_update', {id: me.sale.id, preview: true}),
            jsonData: data,
            method: 'post',
            success: function (response) {
                var res = Ext.decode(response.responseText);

                if (res.success) {
                    me.updateSale(res.sale);
                }

                me.setPreviewMode(true);

                me.layout.setLoading(false);
            }.bind(this)
        });
    },

    updateSale: function(sale) {
        var me = this;

        me.sale = sale;

        if (me.sale.editable) {
            me.saveButton.show();
            me.previewButton.show();
        }
        else {
            me.saveButton.hide();
            me.previewButton.hide();
        }

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
            var me = this;
            var buttons = [{
                iconCls: 'pimcore_icon_reload',
                text: t('reload'),
                handler: function () {
                    this.reload();
                }.bind(this)
            }];
            var items = this.getItems();
            buttons = buttons.concat(this.getTopButtons());

            me.previewButton = Ext.create({
                xtype: 'button',
                iconCls: 'pimcore_icon_seemode',
                text: t('preview'),
                hidden: !this.sale.editable,
                handler: function () {
                    this.preview();
                }.bind(this)
            });
            me.saveButton = Ext.create({
                xtype: 'button',
                iconCls: 'pimcore_icon_save',
                text: t('save'),
                hidden: !this.sale.editable,
                handler: function () {
                    this.save();
                }.bind(this)
            });

            buttons.push(me.previewButton);
            buttons.push(me.saveButton);

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
        return coreshop.order.order.detail.blocks;
    },

    setPreviewMode: function(previewMode) {
        this.previewMode = previewMode;

        var toolbar = this.layout.getDockedItems('toolbar[dock="top"]');

        if (this.previewMode) {
            toolbar[0].addCls('coreshop_preview_mode');
        }
        else {
            toolbar[0].removeCls('coreshop_preview_mode');
        }
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
