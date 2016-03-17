/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.pricerules.abstract');

pimcore.plugin.coreshop.pricerules.abstract = Class.create({

    /**
     * pimcore.plugin.coreshop.pricerules.item
     */
    parent: {},

    data : {},

    type : 'abstract',
    elementType : 'abstract',

    form : null,

    initialize : function (parent, data) {
        this.parent = parent;
        this.data = data;
    },

    getLayout : function () {
        var myId = Ext.id();

        this.layout = new Ext.panel.Panel({
            xparent : this,
            id : myId,
            style: 'margin: 10px 0 0 0',
            tbar : this.getTopBar(t('coreshop_' + this.elementType +  '_' + this.type), myId, this.parent, this.data, 'coreshop_price_rule_icon_' + this.elementType + '_' + this.type),
            items : [
                this.getForm()
            ]
        });

        return this.layout;
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

        return [{
            iconCls: iconCls,
            disabled: true,
            xtype : 'button'
        }, {
            xtype: 'tbtext',
            text: '<b>' + name + '</b>'
        }, '-', {
            iconCls: 'pimcore_icon_up',
            handler: function (blockId, parent, container, namespace) {

                var blockElement = Ext.getCmp(blockId);
                var index = pimcore.plugin.coreshop.pricerule[namespace].abstract.prototype.getIndex(blockElement, container);
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
            }.bind(window, index, parent, container, namespace),
            xtype : 'button'
        }, {
            iconCls: 'pimcore_icon_down',
            handler: function (blockId, parent, container, namespace) {

                var container = container;
                var blockElement = Ext.getCmp(blockId);
                var index = pimcore.plugin.coreshop.pricerule[namespace].abstract.prototype.getIndex(blockElement, container);
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

            }.bind(window, index, parent, container, namespace),
            xtype : 'button'
        }, '->', {
            iconCls: 'pimcore_icon_delete',
            handler: function (index, parent, container, namespace) {
                container.remove(Ext.getCmp(index));
            }.bind(window, index, parent, container, namespace),
            xtype : 'button'
        }];
    },
});
