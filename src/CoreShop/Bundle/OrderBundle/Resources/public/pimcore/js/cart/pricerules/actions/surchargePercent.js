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

pimcore.registerNS('coreshop.cart.pricerules.actions.surchargePercent');
coreshop.cart.pricerules.actions.surchargePercent = Class.create(coreshop.rules.actions.abstract, {

    type: 'surchargePercent',

    getForm: function () {
        var percentValue = 0;
        var applyOnValue = 'total';

        if (this.data) {
            percentValue = this.data.percent;
        }

        var percent = new Ext.form.NumberField({
            fieldLabel: t('coreshop_action_surcharge_percent'),
            name: 'percent',
            value: percentValue,
            minValue: 0,
            maxValue: 100,
            decimalPrecision: 2
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

        this.form = new Ext.form.Panel({
            items: [
                percent,
                applyOn
            ]
        });

        return this.form;
    }
});
