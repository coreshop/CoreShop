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
