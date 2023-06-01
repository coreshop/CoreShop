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

pimcore.registerNS('coreshop.paymentproviderrule.actions.additionPercent');
coreshop.paymentproviderrule.actions.additionPercent = Class.create(coreshop.rules.actions.abstract, {
    type: 'additionPercent',

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
            decimalPrecision: 2
        });
        this.form = new Ext.form.Panel({
            items: [
                percent
            ]
        });

        return this.form;
    }
});
