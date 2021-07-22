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

pimcore.registerNS('coreshop.payment.resource');
coreshop.payment.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.global.addStore('coreshop_payment_provider', 'coreshop/payment_providers');

        coreshop.broker.fireEvent('resource.register', 'coreshop.payment', this);
    },

    openResource: function (item) {
        if (item === 'payment_provider') {
            this.openPaymentProvider();
        }
    },

    openPaymentProvider: function () {
        try {
            pimcore.globalmanager.get('coreshop_payment_providers_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_payment_providers_panel', new coreshop.provider.panel());
        }
    }
});

coreshop.broker.addListener('pimcore.ready', function() {
    new coreshop.payment.resource();
});
