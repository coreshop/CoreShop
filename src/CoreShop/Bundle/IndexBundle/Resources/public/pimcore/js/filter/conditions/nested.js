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

pimcore.registerNS('coreshop.filter.conditions.nested');
coreshop.filter.conditions.nested = Class.create(coreshop.filter.conditions.abstract, {

    type: 'nested',

    operatorCombo: null,
    conditions: null,

    getDefaultItems: function () {
        this.labelField = Ext.create({
            xtype: 'textfield',
            name: 'label',
            width: 400,
            fieldLabel: t('label'),
            value: this.data.label
        });

        return [
            this.labelField
        ];
    },

    getItems: function () {
        this.conditions = new this.parent.__proto__.constructor(this.parent.parent, this.parent.conditions, 'nested');

        var layout = this.conditions.getLayout();
        layout.setTitle(null);
        layout.setIconCls(null);

        // add saved conditions
        if (this.data && this.data.configuration.conditions) {
            Ext.each(this.data.configuration.conditions, function (condition) {
                this.conditions.addCondition(condition.type, condition, false);
            }.bind(this));
        }

        return [new Ext.panel.Panel({
            items: [
                layout
            ]
        })];
    },

    getData: function () {
        var conditions = this.conditions.getData();

        return {
            configuration: {
                conditions: conditions,
            },
            label: this.labelField.getValue()
        };
    }
});
