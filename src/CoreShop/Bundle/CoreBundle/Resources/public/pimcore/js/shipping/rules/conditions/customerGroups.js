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
