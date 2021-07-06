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

pimcore.registerNS('coreshop.cart.pricerules.actions.surchargeAmount');
coreshop.cart.pricerules.actions.surchargeAmount = Class.create(coreshop.rules.actions.abstract, {

    type: 'surchargeAmount',

    getForm: function () {
        var amountValue = 0;
        var currency = null;
        var grossValue = false;
        var applyOnValue = 'total';

        if (this.data) {
            amountValue = this.data.amount / pimcore.globalmanager.get('coreshop.currency.decimal_factor');
            currency = this.data.currency;
            grossValue = this.data.gross;
        }

        var amount = new Ext.form.NumberField({
            fieldLabel: t('coreshop_action_surcharge_amount'),
            name: 'amount',
            value: amountValue,
            decimalPrecision: pimcore.globalmanager.get('coreshop.currency.decimal_precision')
        });

        var gross = new Ext.form.Checkbox({
            fieldLabel: t('coreshop_action_discountAmount_gross'),
            name: 'gross',
            value: grossValue
        });

        this.form = new Ext.form.Panel({
            items: [
                amount,
                gross,
                {
                    xtype: 'coreshop.currency',
                    value: currency
                }
            ]
        });

        return this.form;
    }
});
