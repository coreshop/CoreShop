/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.currency.resource');
coreshop.currency.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.global.addStore('coreshop_currencies', 'coreshop/currencies');
        coreshop.global.addStore('coreshop_exchange_rates', 'coreshop/exchange_rates', [
            {name: 'id'},
            {name: 'fromCurrency'},
            {name: 'toCurrency'},
            {name: 'exchangeRate'}
        ]);

        Ext.Ajax.request({
            url: 'coreshop/currencies/get-config',
            method: 'get',
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);

                    pimcore.globalmanager.add('coreshop.currency.decimal_precision', res.decimal_precision);
                    pimcore.globalmanager.add('coreshop.currency.decimal_factor', res.decimal_factor);
                } catch (e) {

                }
            }.bind(this)
        });

        pimcore.globalmanager.get('coreshop_currencies').load();

        coreshop.broker.fireEvent('resource.register', 'coreshop.currency', this);
    },

    openResource: function (item) {
        if (item === 'currency') {
            this.openCurrencyResource();
        } else if (item === 'exchange_rate') {
            this.openExchangeRateResource();
        }
    },

    openCurrencyResource: function () {
        try {
            pimcore.globalmanager.get('coreshop_currencies_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_currencies_panel', new coreshop.currency.panel());
        }
    },

    openExchangeRateResource: function () {
        try {
            pimcore.globalmanager.get('coreshop_exchange_rates_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_exchange_rates_panel', new coreshop.exchange_rate.panel());
        }
    }
});

coreshop.broker.addListener('pimcore.ready', function() {
    new coreshop.currency.resource();
});
