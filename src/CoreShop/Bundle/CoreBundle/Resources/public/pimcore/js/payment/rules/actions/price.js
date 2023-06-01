/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.paymentproviderrule.actions.price');
coreshop.paymentproviderrule.actions.price = Class.create(coreshop.rules.actions.abstract, {
    type: 'price',

    getForm: function () {
        var priceValue = 0;
        var currency = null;

        if (this.data) {
            priceValue = this.data.price / pimcore.globalmanager.get('coreshop.currency.decimal_factor');
            currency = this.data.currency;
        }

        var price = new Ext.form.NumberField({
            fieldLabel: t('coreshop_action_price'),
            name: 'price',
            value: priceValue,
            decimalPrecision: pimcore.globalmanager.get('coreshop.currency.decimal_precision')
        });

        this.form = new Ext.form.Panel({
            items: [
                price,
                {
                    xtype: 'coreshop.currency',
                    value: currency
                }
            ]
        });

        return this.form;
    }
});
