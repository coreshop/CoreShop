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

pimcore.registerNS('coreshop.shippingrule.conditions.amount');
coreshop.shippingrule.conditions.amount = Class.create(coreshop.rules.conditions.abstract, {
    type: 'amount',

    getForm: function () {
        var minAmountValue = 0;
        var maxAmountValue = 0;
        var grossValue = true;
        var me = this;

        if (this.data && this.data.minAmount) {
            minAmountValue = this.data.minAmount / pimcore.globalmanager.get('coreshop.currency.decimal_factor');
        }

        if (this.data && this.data.maxAmount) {
            maxAmountValue = this.data.maxAmount / pimcore.globalmanager.get('coreshop.currency.decimal_factor');
        }

        if (this.data && this.data.hasOwnProperty('gross')) {
            grossValue = this.data.gross;
        }

        var minAmount = new Ext.form.NumberField({
            fieldLabel: t('coreshop_condition_amount_minAmount'),
            name: 'minAmount',
            value: minAmountValue,
            minValue: 0,
            decimalPrecision: pimcore.globalmanager.get('coreshop.currency.decimal_precision')
        });

        var maxAmount = new Ext.form.NumberField({
            fieldLabel: t('coreshop_condition_amount_maxAmount'),
            name: 'maxAmount',
            value: maxAmountValue,
            minValue: 0,
            decimalPrecision: pimcore.globalmanager.get('coreshop.currency.decimal_precision')
        });

        var gross = new Ext.form.Checkbox({
            fieldLabel: t('coreshop_condition_amount_gross'),
            name: 'gross',
            value: grossValue
        });

        this.form = Ext.create('Ext.form.Panel', {
            items: [
                minAmount, maxAmount, gross
            ]
        });

        return this.form;
    }
});
