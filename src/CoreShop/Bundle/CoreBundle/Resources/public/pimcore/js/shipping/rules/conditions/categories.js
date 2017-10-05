/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.shippingrule.conditions.categories');
coreshop.shippingrule.conditions.categories = Class.create(coreshop.rules.conditions.abstract, {
    type: 'categories',

    categories: null,

    getForm: function () {
        this.categories = new coreshop.object.objectMultihref(this.data ? this.data.categories : [], {
            classes: coreshop.implementations['coreshop.category'],
            name: 'categories',
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
                this.categories.getLayoutEdit()
            ]
        });

        return this.form;
    },

    getValues: function () {
        return {
            categories: this.categories.getValue()
        };
    }
});
