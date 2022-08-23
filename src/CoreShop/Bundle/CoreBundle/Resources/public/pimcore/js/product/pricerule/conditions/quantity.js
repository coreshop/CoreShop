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

pimcore.registerNS('coreshop.product.pricerule.conditions.quantity');
coreshop.product.pricerule.conditions.quantity = Class.create(coreshop.rules.conditions.abstract, {
    type: 'quantity',

    getForm: function () {

        var minQuantityValue = null;
        var maxQuantityValue = 0;
        var currencyValue = null;
        var me = this;

        if (this.data && this.data.minQuantity) {
            minQuantityValue = this.data.minQuantity;
        }

        if (this.data && this.data.maxQuantity) {
            maxQuantityValue = this.data.maxQuantity;
        }

        var minQuantity = new Ext.form.NumberField({
            fieldLabel: t('coreshop_condition_quantity_minQuantity'),
            name: 'minQuantity',
            value: minQuantityValue,
            minValue: 0,
            decimalPrecision: 0,
            step: 1
        });

        var maxQuantity = new Ext.form.NumberField({
            fieldLabel: t('coreshop_condition_quantity_maxQuantity'),
            name: 'maxQuantity',
            value: maxQuantityValue,
            minValue: 0,
            decimalPrecision: 0,
            step: 1
        });

        this.form = Ext.create('Ext.form.Panel', {
            items: [
                minQuantity, maxQuantity
            ]
        });

        return this.form;
    }
});
