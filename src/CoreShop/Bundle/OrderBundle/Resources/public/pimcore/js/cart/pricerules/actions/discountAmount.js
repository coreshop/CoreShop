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

pimcore.registerNS('coreshop.cart.pricerules.actions.discountAmount');
coreshop.cart.pricerules.actions.discountAmount = Class.create(coreshop.rules.actions.abstract, {

    type: 'discountAmount',

    getForm: function () {
        var amountValue = 0;
        var currency = null;
        var grossValue = false;
        var applyOnValue = 'total';

        if (this.data) {
            amountValue = this.data.amount / pimcore.globalmanager.get('coreshop.currency.decimal_factor');
            currency = this.data.currency;
            grossValue = this.data.gross;
            applyOnValue = this.data.applyOn;
        }

        var amount = new Ext.form.NumberField({
            fieldLabel: t('coreshop_action_discount_amount_amount'),
            name: 'amount',
            value: amountValue,
            decimalPrecision: pimcore.globalmanager.get('coreshop.currency.decimal_precision')
        });

        var applyOn = new Ext.form.ComboBox({
            store: [['total', t('coreshop_action_discount_apply_on_total')], ['subtotal', t('coreshop_action_discount_apply_on_subtotal')]],
            triggerAction: 'all',
            typeAhead: false,
            editable: false,
            forceSelection: true,
            queryMode: 'local',
            fieldLabel: t('coreshop_action_discount_apply_on'),
            name: 'applyOn',
            value: applyOnValue
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
                applyOn,
                {
                    xtype: 'coreshop.currency',
                    value: currency
                }
            ]
        });

        return this.form;
    }
});
