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

pimcore.registerNS('coreshop.filter.conditions.combined');

coreshop.filter.conditions.combined = Class.create(coreshop.filter.conditions.abstract, {

    type: 'combined',

    getDefaultItems: function () {
        this.label = Ext.create({
            xtype: 'textfield',
            name: 'label',
            width: 400,
            fieldLabel: t('label'),
            value: this.data.label
        });

        return [
            this.label
        ];
    },

    getItems: function () {
        this.conditions = new this.parent.__proto__.constructor(this.parent.parent, this.parent.conditions, 'combined');

        var layout = this.conditions.getLayout();
        layout.setTitle(null);
        layout.setIconCls(null);

        // add saved conditions
        if (this.data && this.data.conditions) {
            Ext.each(this.data.conditions, function (condition) {
                this.conditions.addCondition(condition.type, condition);
            }.bind(this));
        }

        this.form = new Ext.panel.Panel({
            items: [
                layout
            ]
        });

        return [this.form];
    },

    getData: function () {
        return {
            conditions: this.conditions.getData(),
            label: this.label.getValue()
        };
    }
});
