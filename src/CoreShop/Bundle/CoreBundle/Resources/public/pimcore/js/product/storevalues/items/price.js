/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('coreshop.product.storeValues.items.price');
coreshop.product.storeValues.items.price = Class.create(coreshop.product.storeValues.items.abstract, {

    getForm: function () {

        var price = this.getDataValue('price'),
            priceField = new Ext.form.NumberField({
                fieldLabel: t('coreshop_store_values_store_price'),
                name: 'price',
                componentCls: 'object_field',
                labelWidth: 250,
                minValue: 0,
                value: 0,
                decimalPrecision: pimcore.globalmanager.get('coreshop.currency.decimal_precision')
            });

        // do not fire dirty flag on initial data setup
        priceField.suspendEvents();

        if (price !== null) {
            priceField.setValue(price / pimcore.globalmanager.get('coreshop.currency.decimal_factor'));
            priceField.resetOriginalValue();
            priceField.setFieldLabel(priceField.fieldLabel + ' (' + this.builder.data.currencySymbol + ')');
        }

        if (this.builder.fieldConfig.width) {
            priceField.setWidth(this.builder.fieldConfig.width + priceField.labelWidth);
        } else {
            priceField.setWidth(350 + priceField.labelWidth);
        }

        if (is_numeric(this.builder.fieldConfig['minValue'])) {
            priceField.setMinValue(this.builder.fieldConfig.minValue);
        }

        if (is_numeric(this.builder.fieldConfig['maxValue'])) {
            priceField.setMaxValue(this.builder.fieldConfig.maxValue);
        }

        priceField.resumeEvents(true);

        return priceField;
    }
});
