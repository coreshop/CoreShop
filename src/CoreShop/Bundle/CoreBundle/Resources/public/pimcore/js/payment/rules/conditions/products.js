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

pimcore.registerNS('coreshop.paymentproviderrule.conditions.products');
coreshop.paymentproviderrule.conditions.products = Class.create(coreshop.rules.conditions.abstract, {

    type: 'products',
    products: null,

    getForm: function () {
        this.products = new coreshop.object.objectMultihref(this.data ? this.data.products : [], {
            classes: this.getFormattedStackClasses(coreshop.stack.coreshop.product),
            name: 'products',
            title: '',
            height: 200,
            width: 500,
            columns: [],

            columnType: null,
            datatype: 'data',
            fieldtype: 'objects'
        });

        this.includeVariants = Ext.create({
            xtype: 'checkbox',
            fieldLabel: t('coreshop_condition_include_variants'),
            name: 'include_variants',
            checked: this.data ? this.data.include_variants : false
        });


        this.form = new Ext.form.Panel({
            items: [
                this.products.getLayoutEdit(),
                this.includeVariants
            ]
        });

        return this.form;
    },

    getValues: function () {
        return {
            products: this.products.getValue(),
            include_variants: this.includeVariants.getValue()
        };
    }
});
