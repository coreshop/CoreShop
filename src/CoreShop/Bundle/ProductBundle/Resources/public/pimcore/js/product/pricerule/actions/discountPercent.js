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

pimcore.registerNS('coreshop.product.pricerule.actions.discountPercent');

coreshop.product.pricerule.actions.discountPercent = Class.create(coreshop.rules.actions.abstract, {

    type: 'discountPercent',

    getForm: function () {
        var percentValue = 0;
        var me = this;

        if (this.data) {
            percentValue = this.data.percent;
        }

        var percent = new Ext.form.NumberField({
            fieldLabel: t('coreshop_action_discountPercent_percent'),
            name: 'percent',
            value: percentValue,
            minValue: 0,
            maxValue: 100,
            decimalPrecision: 0
        });
        this.form = new Ext.form.Panel({
            items: [
                percent
            ]
        });

        return this.form;
    }
});
