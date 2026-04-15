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

pimcore.registerNS('coreshop.customer.resource');
coreshop.customer.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.global.addStore('coreshop_customergroups', 'coreshop/customer_groups');

        coreshop.broker.fireEvent('resource.register', 'coreshop.customer', this);
    },

    openResource: function (item) {
        if (item === 'customers') {
            this.openCustomers();
        } else if (item === 'customer_groups') {
            this.openCustomerGroups();
        }
    },

    openCustomers: function () {
        try {
            pimcore.globalmanager.get('coreshop_customer').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_customer', new coreshop.customer.list());
        }
    },

    openCustomerGroups: function () {
        try {
            pimcore.globalmanager.get('coreshop_customer_group').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_customer_group', new coreshop.customer_group.list());
        }
    },
});

coreshop.broker.addListener('pimcore.ready', function() {
    new coreshop.customer.resource();
});
