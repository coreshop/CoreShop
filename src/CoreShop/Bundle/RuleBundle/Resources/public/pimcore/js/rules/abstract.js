/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.rules.abstract');

coreshop.rules.abstract = Class.create({

    /**
     * coreshop.rules.item
     */
    parent: {},

    data: {},

    type: 'abstract',
    elementType: 'abstract',

    form: null,

    initialize: function (parent, type, data) {
        this.parent = parent;
        this.type = type;
        this.id = data && data.hasOwnProperty('id') ? data.id : data;
        this.data = data && data.hasOwnProperty('configuration') ? data.configuration : {};
    },

    getFormattedStackClasses: function (stackClasses) {
        var classes = [];
        if (Ext.isArray(stackClasses)) {
            Ext.Array.each(stackClasses, function (cClass) {
                classes.push({classes: cClass});
            });
        }
        return classes;
    },

    getLayout: function () {
        var myId = Ext.id();

        this.layout = new Ext.panel.Panel({
            xparent: this,
            id: myId,
            style: 'margin: 10px 0 0 0',
            border: true,
            scrollable: true,
            bodyPadding: 10,
            maxHeight: 500,
            tbar: this.getTopBar(t('coreshop_' + this.elementType + '_' + this.type), myId, this.parent, this.data, this.getTopBarIconClass()),
            items: [
                this.getForm()
            ]
        });

        return this.layout;
    },

    getTopBarIconClass: function () {
        return 'coreshop_rule_icon_' + this.elementType + '_' + this.type;
    },

    getForm: function () {
        return {};
    },

    getIndex: function (blockElement, container) {
        // detect index
        var index;

        for (var s = 0; s < container.items.items.length; s++) {
            if (container.items.items[s].getId() == blockElement.getId()) {
                index = s;
                break;
            }
        }

        return index;
    },

    /**
     * @param name
     * @param index
     * @param parent
     * @param data
     * @param iconCls
     * @returns {Array}
     */
    getTopBar: function (name, index, parent, data, iconCls) {
        var namespace = '';
        var container = null;

        if (this.elementType == 'action') {
            namespace = 'actions';
            container = parent.actionsContainer;
        } else if (this.elementType == 'condition') {
            namespace = 'conditions';
            container = parent.conditionsContainer;
        }

        var items = [{
            iconCls: iconCls,
            disabled: true,
            xtype: 'button'
        }, {
            xtype: 'tbtext',
            text: '<b>' + name + '</b>'
        }, '-', {
            iconCls: 'pimcore_icon_up',
            handler: function (blockId, parent, container, namespace) {

                var blockElement = Ext.getCmp(blockId);
                var index = coreshop.rules[namespace].abstract.prototype.getIndex(blockElement, container);

                var newIndex = index - 1;
                if (newIndex < 0) {
                    newIndex = 0;
                }

                this.parent.setDirty(true);

                // move this node temorary to an other so ext recognizes a change
                container.remove(blockElement, false);
                container.insert(newIndex, blockElement);

                container.updateLayout();

                pimcore.layout.refresh();
            }.bind(this, index, parent, container, namespace),
            xtype: 'button'
        }, {
            iconCls: 'pimcore_icon_down',
            handler: function (blockId, parent, container, namespace) {

                var container = container;
                var blockElement = Ext.getCmp(blockId);
                var index = coreshop.rules[namespace].abstract.prototype.getIndex(blockElement, container);

                this.parent.setDirty(true);

                // move this node temorary to an other so ext recognizes a change
                container.remove(blockElement, false);
                container.insert(index + 1, blockElement);
                container.updateLayout();

                pimcore.layout.refresh();

            }.bind(this, index, parent, container, namespace),
            xtype: 'button'
        }];


        if (Ext.isFunction(this.getTopBarItems)) {
            items.push.apply(items, this.getTopBarItems());
        }

        items.push.apply(items, [
            '->', {
                iconCls: 'pimcore_icon_delete',
                handler: function (index, parent, container, namespace) {
                    parent.setDirty(true);
                    container.remove(Ext.getCmp(index));

                    this.parent.setDirty(true);
                }.bind(this, index, parent, container, namespace),
                xtype: 'button'
            }
        ]);

        return items;
    }
});
