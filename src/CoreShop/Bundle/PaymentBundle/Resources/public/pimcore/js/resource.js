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

pimcore.registerNS('coreshop.payment.resource');
coreshop.payment.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.global.addStoreWithRoute('coreshop_payment_provider', 'coreshop_payment_provider_list', [
            {name: 'id'},
            {name: 'identifier'}
        ]);

        coreshop.global.addStoreWithRoute('coreshop_payment_provider_rules', 'coreshop_payment_provider_rule_list');

        coreshop.broker.fireEvent('resource.register', 'coreshop.payment', this);
    },

    openResource: function (item) {
        if (item === 'payment_provider') {
            this.openPaymentProvider();
        }else if (item === 'payment_provider_rule') {
            this.openPaymentProviderRules();
        }
    },

    openPaymentProvider: function () {
        try {
            pimcore.globalmanager.get('coreshop_payment_providers_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_payment_providers_panel', new coreshop.provider.panel());
        }
    },

    openPaymentProviderRules: function () {
        try {
            pimcore.globalmanager.get('coreshop_payment_providers_panel_rules').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_payment_providers_panel_rules', new coreshop.paymentproviderrule.panel());
        }
    }
});

coreshop.broker.addListener('pimcore.ready', function() {
    new coreshop.payment.resource();
});
