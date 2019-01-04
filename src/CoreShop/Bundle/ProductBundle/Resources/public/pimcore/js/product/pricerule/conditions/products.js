/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.product.pricerule.conditions.products');
coreshop.product.pricerule.conditions.products = Class.create(coreshop.rules.conditions.abstract, {

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

        this.form = new Ext.form.Panel({
            items: [
                this.products.getLayoutEdit()
            ]
        });

        return this.form;
    },

    getValues: function () {
        return {
            products: this.products.getValue()
        };
    }
});
