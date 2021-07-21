/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
