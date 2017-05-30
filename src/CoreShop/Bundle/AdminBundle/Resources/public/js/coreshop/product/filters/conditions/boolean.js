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

pimcore.registerNS('pimcore.plugin.coreshop.filters.conditions.boolean');

pimcore.plugin.coreshop.filters.conditions.boolean = Class.create(pimcore.plugin.coreshop.filters.conditions.abstract, {

    type : 'boolean',

    getDefaultItems : function () {
        this.valueStore = new Ext.data.ArrayStore({
            proxy: new Ext.data.HttpProxy({
                url : '/admin/coreshop/filters/get-values-for-filter-field'
            }),
            reader: new Ext.data.JsonReader({}, [
                { name:'value' }
            ])
        });

        this.fieldsCombo = Ext.create({
            xtype: 'combo',
            fieldLabel: t('coreshop_product_filters_fields'),
            name: 'field',
            width: 400,
            store: this.parent.getFieldsStore(),
            displayField : 'name',
            valueField : 'name',
            triggerAction: 'all',
            multiSelect:true,
            typeAhead: false,
            editable: false,
            forceSelection: true,
            queryMode: 'local',
            value : this.data.field,
            listeners : {
                change : function (combo, newValue) {
                    this.onFieldChange.call(this, combo, newValue);
                }.bind(this)
            }
        });

        if (this.data.field) {
            this.onFieldChange(this.fieldsCombo, this.data.field);
        }

        return [
            {
                xtype : 'textfield',
                name : 'label',
                width : 400,
                fieldLabel : t('label'),
                value : this.data.label
            },
            this.fieldsCombo
        ];
    },

    getItems : function ()
    {
        return [
            {
                xtype: 'combo',
                fieldLabel: t('coreshop_product_filters_values'),
                name: 'preSelects',
                width: 400,
                store: this.parent.getFieldsStore(),
                displayField : 'name',
                multiSelect:true,
                valueField : 'name',
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                queryMode: 'local',
                value : this.data.preSelects
            }
        ];
    }
});
