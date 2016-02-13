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

pimcore.registerNS("pimcore.plugin.coreshop.filters.abstract");

pimcore.plugin.coreshop.filters.abstract = Class.create({

    /**
     * pimcore.plugin.coreshop.filters.item
     */
    parent: {},

    data : {},

    type : 'abstract',

    form : null,

    initialize : function(parent, data) {
        this.parent = parent;
        this.data = data;
    },

    getLayout : function() {
        var myId = Ext.id();

        var items = this.getDefaultItems();

        items.push.apply(items, this.getItems());

        this.form = new Ext.form.Panel({
            xparent : this,
            id : myId,
            style: "margin: 10px 0 0 0",
            tbar : this.getTopBar(t("coreshop_product_filters_" + this.type), myId, this.parent, this.data, "coreshop_product_filters_icon_condition_" + this.type),
            items : [
                {
                    xtype : 'fieldset',
                    items : items
                }
            ]
        });

        return this.form;
    },

    getDefaultItems : function() {
        this.valueStore = new Ext.data.ArrayStore({
            proxy: new Ext.data.HttpProxy({
                url : '/plugin/CoreShop/admin_Filter/get-values-for-filter-field'
            }),
            reader: new Ext.data.JsonReader({}, [
                {name:'value'}
            ])
        });

        this.fieldsCombo = Ext.create({
            xtype: "combo",
            fieldLabel: t('coreshop_product_filters_field'),
            name: "field",
            width: 400,
            store: this.parent.getFieldsStore(),
            displayField : 'name',
            valueField : 'name',
            triggerAction: "all",
            typeAhead: false,
            editable: false,
            forceSelection: true,
            queryMode: "local",
            value : this.data.field,
            listeners : {
                change : function(combo, newValue) {
                    this.onFieldChange.call(this, combo, newValue);
                }.bind(this)
            }
        });

        if(this.data.field) {
            this.onFieldChange(this.fieldsCombo, this.data.field);
        }

        return [
            {
                xtype : 'textfield',
                name : 'label',
                width : 400,
                fieldLabel : t('label')
            },
            this.fieldsCombo
        ];
    },

    onFieldChange : function(combo, newValue) {
        this.valueStore.proxy.extraParams = {
            field : newValue,
            index : combo.getStore().proxy.extraParams['index']
        };

        this.valueStore.load({
            params : this.valueStore.proxy.extraParams
        });
    },

    getIndex: function (blockElement, container) {
        // detect index
        var index;

        for(var s=0; s < container.items.items.length; s++) {
            if(container.items.items[s].getId() == blockElement.getId()) {
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
        var container = parent.conditionsContainer;

        return [{
            iconCls: iconCls,
            disabled: true,
            xtype : 'button'
        }, {
            xtype: "tbtext",
            text: "<b>" + name + "</b>"
        },"-",{
            iconCls: "pimcore_icon_up",
            handler: function (blockId, parent, container) {

                var blockElement = Ext.getCmp(blockId);
                var index = pimcore.plugin.coreshop.filters.conditions.abstract.prototype.getIndex(blockElement, container);
                var tmpContainer = pimcore.viewport;

                var newIndex = index-1;
                if(newIndex < 0) {
                    newIndex = 0;
                }

                // move this node temorary to an other so ext recognizes a change
                container.remove(blockElement, false);
                tmpContainer.add(blockElement);
                container.updateLayout();
                tmpContainer.updateLayout();

                // move the element to the right position
                tmpContainer.remove(blockElement,false);
                container.insert(newIndex, blockElement);
                container.updateLayout();
                tmpContainer.updateLayout();

                pimcore.layout.refresh();
            }.bind(window, index, parent, container),
            xtype : 'button'
        },{
            iconCls: "pimcore_icon_down",
            handler: function (blockId, parent, container) {

                var container = container;
                var blockElement = Ext.getCmp(blockId);
                var index = pimcore.plugin.coreshop.filters.conditions.abstract.prototype.getIndex(blockElement, container);
                var tmpContainer = pimcore.viewport;

                // move this node temorary to an other so ext recognizes a change
                container.remove(blockElement, false);
                tmpContainer.add(blockElement);
                container.updateLayout();
                tmpContainer.updateLayout();

                // move the element to the right position
                tmpContainer.remove(blockElement,false);
                container.insert(index+1, blockElement);
                container.updateLayout();
                tmpContainer.updateLayout();

                pimcore.layout.refresh();

            }.bind(window, index, parent, container),
            xtype : 'button'
        },"->",{
            iconCls: "pimcore_icon_delete",
            handler: function (index, parent, container) {
                container.remove(Ext.getCmp(index));
            }.bind(window, index, parent, container),
            xtype : 'button'
        }];
    },
});
