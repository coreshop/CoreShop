/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.pimcore.coreExtension.embeddedClassItemContainer');

coreshop.pimcore.coreExtension.embeddedClassItemContainer = Class.create({
    panel: null,
    icon: 'pimcore_icon_object',
    dirty: false,
    removed: false,

    initialize: function (parentPanel, objectEdit, noteditable, layout, objectMetaData, icon) {
        this.parentPanel = parentPanel;
        this.noteditable = noteditable;
        this.index = objectMetaData.hasOwnProperty('index') ? objectMetaData.index : false;
        this.objectEdit = objectEdit;
        this.layout = layout;
        this.objectMetaData = objectMetaData;
        this.icon = icon;
    },

    getLayout: function () {
        var itemLayout = this.objectEdit.getLayout(this.layout);

        this.layout = new Ext.panel.Panel({
            blockClass: this,
            objectEdit: this.objectEdit,
            style: 'margin: 10px 0 0 0',
            border: true,
            scrollable: true,
            bodyPadding: 10,
            tbar: this.getTopBar(this.objectMetaData['o_className'] + ': ' + this.objectMetaData['index'], this.icon),
            items: [
                itemLayout
            ]
        });

        return this.layout;
    },

    isRemoved: function() {
        return this.removed;
    },

    isDirty: function () {
        return this.dirty;
    },

    getForm: function () {
        return {};
    },

    getIndex: function() {
        return this.index;
    },

    setIndex: function(index) {
        this.index = index;

        this.updateIndex();
    },

    getCurrentIndex: function () {
        // detect index
        var me = this,
            container = me.parentPanel.container,
            blockElement = me.layout,
            s;

        for (s = 0; s < container.items.items.length; s++) {
            if (container.items.items[s].getId() === blockElement.getId()) {
                return s;
            }
        }

        return null;
    },

    updateIndex: function() {
        this.layout.getDockedItems('toolbar[dock="top"]')[0].down('tbtext').setText(
            this.objectMetaData['o_className'] + ': ' + this.getIndex()
        );
    },

    getTopBar: function (name, iconCls) {
        var me = this,
            items = [
                {
                    iconCls: iconCls,
                    disabled: true,
                    xtype: 'button'
                },
                {
                    xtype: 'tbtext',
                    text: '<b>' + name + '</b>'
                },
                '-'
            ];

        if (!this.noteditable) {
            items.push({
                iconCls: 'pimcore_icon_up',
                handler: function (blockId, parent, container) {
                    var blockElement = me.layout,
                        prevElement = blockElement.previousSibling();

                    if (prevElement) {
                        me.parentPanel.container.moveBefore(blockElement, prevElement);
                        me.dirty = true;

                        me.updateIndex();
                        prevElement.blockClass.updateIndex();
                    }
                },
                xtype: 'button'
            });
            items.push({
                iconCls: 'pimcore_icon_down',
                handler: function (blockId, parent, container) {
                    var blockElement = me.layout,
                        nextElement = blockElement.nextSibling();

                    if (nextElement) {
                        me.parentPanel.container.moveAfter(blockElement, nextElement);
                        me.dirty = true;

                        me.updateIndex();
                        nextElement.blockClass.updateIndex();
                    }
                },
                xtype: 'button'
            });
            items.push('->');
            items.push({
                iconCls: 'pimcore_icon_delete',
                handler: function () {
                    me.parentPanel.container.remove(me.layout);
                    me.dirty = true;
                    me.removed = true;
                },
                xtype: 'button'
            });
        }

        return items;
    }
});