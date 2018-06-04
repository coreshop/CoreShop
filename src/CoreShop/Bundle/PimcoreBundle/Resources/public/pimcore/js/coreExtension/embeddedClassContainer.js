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

pimcore.registerNS('coreshop.pimcore.coreExtension.embeddedClassContainer');

coreshop.pimcore.coreExtension.embeddedClassContainer = Class.create({

    itemContainers: [],

    initialize: function (tag, noteditable) {
        this.tag = tag;
        this.noteditable = noteditable;
        this.itemContainers = [];
    },

    getLayout: function () {
        var toolbarItems = [
            {
                xtype: 'tbspacer',
                width: 20,
                height: 16,
                cls: 'coreshop_icon_embedded_class'
            },
            {
                xtype: "tbtext",
                text: "<b>" + t(this.tag.fieldConfig.title) + "</b>"
            }
        ];

        if (!this.noteditable) {
            toolbarItems.push("->");
            toolbarItems.push({
                iconCls: 'pimcore_icon_add',
                handler: function () {
                    this.tag.createNew();
                }.bind(this)
            });
        }

        this.container = new Ext.Panel({
            autoScroll: true,
            forceLayout: true,
            style: 'padding: 10px',
            tbar: toolbarItems,
            border: false
        });

        return this.container;
    },

    destroy: function () {
        if (this.container) {
            this.container.destroy();
        }
    },

    add: function (objectEdit, layout, general, icon) {
        var itemContainer = new coreshop.pimcore.coreExtension.embeddedClassItemContainer(this, objectEdit, this.noteditable, layout, general, icon);

        this.itemContainers.push(itemContainer);

        this.container.add(itemContainer.getLayout());
    },

    getItems: function() {
        return this.itemContainers;
    },

    getLayouts: function () {
        return this.container.items.items.map(function (item) {
            return item.initialConfig.objectEdit;
        });
    }
});
