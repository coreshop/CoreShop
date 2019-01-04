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

pimcore.registerNS('coreshop.customer.resource');
coreshop.customer.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.global.addStore('coreshop_customergroups', 'coreshop/customer_groups');

        coreshop.broker.fireEvent('resource.register', 'coreshop.customer', this);
    }
});

coreshop.broker.addListener('pimcore.ready', function() {
    new coreshop.customer.resource();
});