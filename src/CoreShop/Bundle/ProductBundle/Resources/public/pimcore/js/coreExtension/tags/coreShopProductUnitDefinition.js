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

pimcore.registerNS('pimcore.object.tags.coreShopProductUnitDefinition');
pimcore.object.tags.coreShopProductUnitDefinition = Class.create(pimcore.object.tags.abstract, {
    type: 'coreShopProductUnitDefinition',

    allowEmpty: false,
    displayField: 'unitName',

    initialize: function (data, fieldConfig) {
        this.data = data;
        this.fieldConfig = fieldConfig;
        this.fieldConfig.width = 350;
    },

    getLayoutShow: function () {

        this.component = this.getLayoutEdit();
        this.component.setReadOnly(true);

        return this.component;
    },

    getValue: function () {
        return this.data.id;
    },

    getLayoutEdit: function () {

        var options, displayValue;

        displayValue = this.data.id !== null ? this.data.unitName + ' (' + this.data.conversationRate + ')' : null;

        options = {
            name: this.fieldConfig.name,
            fieldLabel: this.fieldConfig.title,
            componentCls: 'object_field',
            fieldCls: 'pimcore_droptarget_unit_definition_input',
            width: 250,
            labelWidth: 100,
            displayField: this.displayField,
            valueField: 'id',
            queryMode: 'local',
            value: displayValue,
        };

        if (this.fieldConfig.labelWidth) {
            options.labelWidth = this.fieldConfig.labelWidth;
        }

        if (this.fieldConfig.width) {
            options.width = this.fieldConfig.width;
        }

        options.width += options.labelWidth;

        this.component = new Ext.form.TextField(options);

        return this.component;
    }

});
