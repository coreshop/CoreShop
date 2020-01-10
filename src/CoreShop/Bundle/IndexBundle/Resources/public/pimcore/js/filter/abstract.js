/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.filter.abstract');

coreshop.filter.abstract = Class.create({

    /**
     * coreshop.filter.item
     */
    parent: {},
    data: {},

    type: 'abstract',
    elementType: 'abstract',

    form: null,

    initialize: function (parent, data, index) {
        this.parent = parent;
        this.data = data;

        if (!data.hasOwnProperty('configuration')) {
            data.configuration = {};
        }
    },

    getLayout: function () {
        var myId = Ext.id();

        var items = this.getDefaultItems();

        this.form = new Ext.form.Panel({
            items: items
        });

        this.configurationForm = new Ext.form.Panel({
            items: this.getItems()
        });

        return new Ext.panel.Panel({
            xparent: this,
            bodyPadding: 10,
            id: myId,
            style: 'margin: 10px 0 0 0',
            tbar: this.getTopBar(t('coreshop_filters_' + this.type), myId, this.parent, this.data, 'coreshop_filters_icon_' + this.elementType + '_' + this.type),
            border: true,
            items: [
                this.form,
                this.configurationForm
            ]
        });
    },

    getData: function () {
        var data = this.form.getForm().getFieldValues();

        data['configuration'] = this.configurationForm.getForm().getFieldValues();

        return data;
    },

    getFieldsComboBox: function (fieldName) {
        fieldName = Ext.isDefined(fieldName) ? fieldName : 'field';
        var comboName = fieldName + 'sCombo';

        if (!this[comboName]) {

            this.valueStore = new Ext.data.ArrayStore({
                proxy: new Ext.data.HttpProxy({
                    url: '/admin/coreshop/filters/get-values-for-filter-field'
                }),
                reader: new Ext.data.JsonReader({}, [
                    {name: 'value'},
                    {name: 'key'}
                ])
            });

            this[comboName] = Ext.create({
                xtype: 'combo',
                fieldLabel: t('coreshop_filters_' + fieldName),
                name: fieldName,
                width: 400,
                store: this.parent.getFieldsStore(),
                displayField: 'name',
                valueField: 'name',
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                queryMode: 'local',
                value: this.data.configuration.hasOwnProperty(fieldName) ? this.data.configuration[fieldName] : null,
                listeners: {
                    change: function (combo, newValue) {
                        this.onFieldChange.call(this, combo, newValue);
                    }.bind(this)
                }
            });
        }

        if (this.data.configuration.hasOwnProperty(fieldName) && this.data.configuration[fieldName]) {
            this.onFieldChange(this[comboName], this.data.configuration[fieldName]);
        }

        return this[comboName];
    },

    onFieldChange: function (combo, newValue) {
        this.valueStore.proxy.extraParams = {
            field: newValue,
            index: combo.getStore().proxy.extraParams['index']
        };

        this.valueStore.load({
            params: this.valueStore.proxy.extraParams
        });
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
        var container = parent.fieldsContainer;

        return [{
            iconCls: iconCls,
            disabled: true,
            xtype: 'button'
        }, {
            xtype: 'tbtext',
            text: '<b>' + name + '</b>'
        }, '-', {
            iconCls: 'pimcore_icon_up',
            handler: function (blockId, parent, container) {

                var blockElement = Ext.getCmp(blockId);
                var index = coreshop.filter[this.elementType].abstract.prototype.getIndex(blockElement, container);
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
            }.bind(this, index, parent, container),
            xtype: 'button'
        }, {
            iconCls: 'pimcore_icon_down',
            handler: function (blockId, parent, container) {

                var container = container;
                var blockElement = Ext.getCmp(blockId);
                var index = coreshop.filter[this.elementType].abstract.prototype.getIndex(blockElement, container);
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

            }.bind(this, index, parent, container),
            xtype: 'button'
        }, '->', {
            iconCls: 'pimcore_icon_delete',
            handler: function (index, parent, container) {
                container.remove(Ext.getCmp(index));
            }.bind(this, index, parent, container),
            xtype: 'button'
        }];
    }
});
