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

pimcore.registerNS('coreshop.shippingrule.conditions.customers');
coreshop.shippingrule.conditions.customers = Class.create(coreshop.rules.conditions.abstract, {

    type: 'customers',
    customers: null,

    getForm: function () {
        this.customers = new coreshop.object.objectMultihref(this.data ? this.data.customers : [], {
            classes: this.getFormattedStackClasses(coreshop.stack.coreshop.customer),
            name: 'customers',
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
                this.customers.getLayoutEdit()
            ]
        });

        return this.form;
    },

    getValues: function () {
        return {
            customers: this.customers.getValue()
        };
    }
});
