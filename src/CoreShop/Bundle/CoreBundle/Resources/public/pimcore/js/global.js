/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.global');
coreshop.global = {

    settings: {},

    initialize: function (settings) {
        this.settings = settings;

        this._initStores();
    },

    _initStores: function () {
        this._createStore('coreshop_currencies', 'currencies');
        this._createStore('coreshop_zones', 'zones', [
            {name: 'id'},
            {name: 'name'},
            {name: 'active'}
        ]);
        this._createStore('coreshop_countries', 'countries');
        this._createStore('coreshop_tax_rates', 'tax_rates', [
            {name: 'id'},
            {name: 'name'},
            {name: 'rate'}
        ]);
        this._createStore('coreshop_taxrulegroups', 'tax_rule_groups');
        this._createStore('coreshop_customergroups', 'customer-groups');
        this._createStore('coreshop_carriers', 'carriers');
        this._createStore('coreshop_cart_price_rules', 'cart_price_rules');
        this._createStore('coreshop_indexes', 'indices');
        this._createStore('coreshop_product_filters', 'filters');
        this._createStore('coreshop_states', 'states');
        //this._createStore('coreshop_messaging_contacts', 'messaging-contacts');
        //this._createStore('coreshop_messaging_thread_states', 'messaging-thread-states');
        this._createStore('coreshop_stores', 'stores');
        this._createStore('coreshop_carrier_shipping_rules', 'shipping_rules');
        this._createStore('coreshop_notification_rules', 'notification_rules');
        this._createStore('coreshop_payment_provider', 'payment_providers');

        pimcore.globalmanager.get('coreshop_tax_rates').load();
        pimcore.globalmanager.get('coreshop_countries').load();
        pimcore.globalmanager.get('coreshop_states').load();
        pimcore.globalmanager.get('coreshop_zones').load();
        pimcore.globalmanager.get('coreshop_currencies').load();
        pimcore.globalmanager.get('coreshop_stores').load();
        pimcore.globalmanager.get('coreshop_carriers').load();
        //pimcore.globalmanager.get('coreshop_messaging_contacts').load();
        //pimcore.globalmanager.get('coreshop_messaging_thread_states').load();

        pimcore.globalmanager.add('coreshop_order_states', new Ext.data.JsonStore({
            data: this.settings.orderStates,
            fields: ['name', 'label', 'color'],
            idProperty: 'name'
        }));
    },

    _createStore: function (name, url, fields) {
        var proxy = new Ext.data.HttpProxy({
            url: '/admin/coreshop/' + url + '/list'
        });

        if (!fields) {
            fields = [
                {name: 'id'},
                {name: 'name'}
            ];
        }

        var reader = new Ext.data.JsonReader({}, fields);

        var store = new Ext.data.Store({
            restful: false,
            proxy: proxy,
            reader: reader,
            autoload: true
        });

        pimcore.globalmanager.add(name, store);
    }
};

if (!String.prototype.format) {
    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] !== 'undefined'
                ? args[number]
                : match
                ;
        });
    };
}
