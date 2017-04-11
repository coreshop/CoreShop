/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.rules.conditions.amount');

pimcore.plugin.coreshop.rules.conditions.amount = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {

    type : 'amount',

    getForm : function ()
    {
        var minAmountValue = null;
        var maxAmountValue = 0;
        var me = this;

        if (this.data && this.data.minAmount) {
            minAmountValue = this.data.minAmount;
        }

        if (this.data && this.data.maxAmount) {
            maxAmountValue = this.data.maxAmount;
        }

        var minAmount = new Ext.form.NumberField({
            fieldLabel:t('coreshop_condition_amount_minAmount'),
            name:'minAmount',
            value : minAmountValue,
            minValue : 0,
            decimalPrecision : 0,
            step : 1
        });

        var maxAmount = new Ext.form.NumberField({
            fieldLabel:t('coreshop_condition_amount_maxAmount'),
            name:'maxAmount',
            value : maxAmountValue,
            minValue : 0,
            decimalPrecision : 0,
            step : 1
        });

        this.form = Ext.create('Ext.form.Panel', {
            items : [
                minAmount, maxAmount
            ]
        });

        return this.form;
    }
});
