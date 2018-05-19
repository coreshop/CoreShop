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

pimcore.registerNS('coreshop.pimcore.coreExtension.embeddedClassItemContainer');

coreshop.pimcore.coreExtension.embeddedClassItemContainer = Class.create({
    panel: null,
    icon: 'pimcore_icon_object',

    initialize: function (parentPanel, objectEdit, noteditable, layout, objectMetaData, icon) {
        this.parentPanel = parentPanel;
        this.noteditable = noteditable;
        this.objectEdit = objectEdit;
        this.layout = layout;
        this.objectMetaData = objectMetaData;
        this.icon = icon;
    },

    getLayout: function () {
        var myId = Ext.id(),
            itemLayout = this.objectEdit.getLayout(this.layout);

        this.layout = new Ext.panel.Panel({
            objectEdit: this.objectEdit,
            id: myId,
            style: 'margin: 10px 0 0 0',
            border: true,
            scrollable: true,
            bodyPadding: 10,
            tbar: this.getTopBar(this.objectMetaData['o_className'] + ': ' + this.objectMetaData['o_key'], myId, this.parentPanel, this.icon),
            items: [
                itemLayout
            ]
        });

        return this.layout;
    },

    getForm: function () {
        return {};
    },

    getIndex: function (blockElement, container) {
        // detect index
        var index;

        for (var s = 0; s < container.items.items.length; s++) {
            if (container.items.items[s].getId() === blockElement.getId()) {
                index = s;
                break;
            }
        }

        return index;
    },


    getTopBar: function (name, index, parent, iconCls) {
        var me = this,
            container = parent.container;

        var items = [
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
                    var blockElement = Ext.getCmp(blockId);
                    var index = me.getIndex(blockElement, container);
                    var tmpContainer = pimcore.viewport;

                    var newIndex = index - 1;
                    if (newIndex < 0) {
                        newIndex = 0;
                    }

                    // move this node temorary to an other so ext recognizes a change
                    container.remove(blockElement, false);
                    tmpContainer.add(blockElement);
                    container.updateLayout();
                    tmpContainer.updateLayout();

                    // move the element to the right position
                    tmpContainer.remove(blockElement, false);
                    container.insert(newIndex, blockElement);
                    container.updateLayout();
                    tmpContainer.updateLayout();

                    pimcore.layout.refresh();
                }.bind(window, index, parent, container),
                xtype: 'button'
            });
            items.push({
                iconCls: 'pimcore_icon_down',
                handler: function (blockId, parent, container) {
                    var blockElement = Ext.getCmp(blockId);
                    var index = me.getIndex(blockElement, container);
                    var tmpContainer = pimcore.viewport;

                    // move this node temorary to an other so ext recognizes a change
                    container.remove(blockElement, false);
                    tmpContainer.add(blockElement);
                    container.updateLayout();
                    tmpContainer.updateLayout();

                    // move the element to the right position
                    tmpContainer.remove(blockElement, false);
                    container.insert(index + 1, blockElement);
                    container.updateLayout();
                    tmpContainer.updateLayout();

                    pimcore.layout.refresh();

                }.bind(window, index, parent, container),
                xtype: 'button'
            });
            items.push('->');
            items.push({
                iconCls: 'pimcore_icon_delete',
                handler: function (index, parent, container) {
                    container.remove(Ext.getCmp(index));
                }.bind(window, index, parent, container),
                xtype: 'button'
            });
        }

        return items;
    }
});