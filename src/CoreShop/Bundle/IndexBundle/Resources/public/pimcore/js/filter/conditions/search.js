/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.filter.conditions.category_search');

coreshop.filter.conditions.search = Class.create(coreshop.filter.conditions.abstract, {
    type: 'search',

    getDefaultItems: function () {
        return [
            {
                xtype: 'textfield',
                name: 'label',
                width: 400,
                fieldLabel: t('label'),
                value: this.data.label
            }
        ];
    },

    getItems: function () {
        var concatenators = Ext.create('Ext.data.Store', {
            fields: ['value', 'name'],
            data : [
                {"value":"OR", "name":"OR"},
                {"value":"AND", "name":"AND"}
            ]
        });

        var patterns = Ext.create('Ext.data.Store', {
            fields: ['value', 'name'],
            data : [
                {"value":"left", "name":"Ends with"},
                {"value":"right", "name":"Begins with"},
                {"value":"both", "name":"Contains"}
            ]
        });

        return [
            this.getFieldsComboBox(),
            {
                xtype: 'textfield',
                fieldLabel: t('coreshop_filters_search_term'),
                name: 'searchTerm',
                width: 400,
                value: this.data.configuration.searchTerm
            },
            Ext.create('Ext.form.ComboBox', {
                name: 'pattern',
                fieldLabel: 'Choose pattern',
                store: patterns,
                queryMode: 'local',
                displayField: 'name',
                valueField: 'value',
                value: this.data.configuration.pattern ? this.data.configuration.pattern : 'both',
                renderTo: Ext.getBody()
            }),
            Ext.create('Ext.form.ComboBox', {
                name: 'concatenator',
                fieldLabel: 'Choose concatenator',
                store: concatenators,
                queryMode: 'local',
                displayField: 'name',
                valueField: 'value',
                value: this.data.configuration.concatenator ? this.data.configuration.concatenator : concatenators.first(),
                renderTo: Ext.getBody()
            })
        ];
    },

    getFieldsComboBox: function (fieldName) {
        fieldName = Ext.isDefined(fieldName) ? fieldName : 'fields';
        var comboName = fieldName + 'sCombo';

        if (!this[comboName]) {
            this[comboName] = Ext.create({
                xtype: 'combo',
                fieldLabel: t('coreshop_filters_' + fieldName),
                name: fieldName,
                width: 400,
                store: this.parent.getFieldsStore(),
                displayField: 'name',
                multiSelect: true,
                valueField: 'name',
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                queryMode: 'local',
                listeners: {
                    // not working with setting value, had to use afterrender
                    afterrender: function() {
                        this[comboName].setValue(this.data.configuration[fieldName] ? this.data.configuration[fieldName] : null)
                    }.bind(this)
                }
            });
        }

        return this[comboName];
    },
});
