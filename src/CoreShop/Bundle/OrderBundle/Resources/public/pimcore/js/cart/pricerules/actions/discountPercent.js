/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.cart.pricerules.actions.discountPercent');
coreshop.cart.pricerules.actions.discountPercent = Class.create(coreshop.rules.actions.abstract, {

    type: 'discountPercent',

    getForm: function () {
        var percentValue = 0;
        var me = this;

        if (this.data) {
            percentValue = this.data.percent;
        }

        var percent = new Ext.form.NumberField({
            fieldLabel: t('coreshop_action_discount_percent_percent'),
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
