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

pimcore.registerNS('coreshop.shippingrule.conditions.customerGroups');
coreshop.shippingrule.conditions.customerGroups = Class.create(coreshop.rules.conditions.abstract, {

    type: 'customerGroups',
    customerGroups: null,

    getForm: function () {
        this.customerGroups = new coreshop.object.objectMultihref(this.data ? this.data.customerGroups : [], {
            classes: this.getFormattedStackClasses(coreshop.stack.coreshop.customer_group),
            name: 'customerGroups',
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
                this.customerGroups.getLayoutEdit()
            ]
        });

        return this.form;
    },

    getValues: function () {
        return {
            customerGroups: this.customerGroups.getValue()
        };
    }
});
