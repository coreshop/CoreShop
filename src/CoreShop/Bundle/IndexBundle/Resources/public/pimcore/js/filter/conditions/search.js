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
                {"value":"left", "name":t('coreshop_filters_search_patterns_left')},
                {"value":"right", "name":t('coreshop_filters_search_patterns_right')},
                {"value":"both", "name":t('coreshop_filters_search_patterns_both')}
            ]
        });

        return [
            {
                xtype: 'textfield',
                name: 'name',
                width: 400,
                fieldLabel: t('coreshop_filters_search_condition_name'),
                value: this.data.configuration.name
            },
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
                fieldLabel: t('coreshop_filters_search_patterns_label'),
                store: patterns,
                queryMode: 'local',
                displayField: 'name',
                valueField: 'value',
                value: this.data.configuration.pattern ? this.data.configuration.pattern : 'both',
                renderTo: Ext.getBody()
            }),
            Ext.create('Ext.form.ComboBox', {
                name: 'concatenator',
                fieldLabel: t('coreshop_filters_search_patterns_concatenator'),
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
