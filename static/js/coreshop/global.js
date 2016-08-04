/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.global');
pimcore.plugin.coreshop.global = {

    settings : {},

    initialize : function (settings)
    {
        this.settings = settings;

        if (intval(this.settings.systemSettings['SYSTEM.ISINSTALLED'])) {
            this._initStores();
        }
    },

    _initStores : function () {
        this._createStore('coreshop_currencies', 'currency');
        this._createStore('coreshop_zones', 'zone', [
            { name:'id' },
            { name:'name' },
            { name:'active' }
        ]);
        this._createStore('coreshop_countries', 'country');
        this._createStore('coreshop_orderstates', 'order-state');
        this._createStore('coreshop_taxes', 'Tax', [
            { name:'id' },
            { name:'name' },
            { name:'rate' }
        ]);
        this._createStore('coreshop_taxrulegroups', 'tax-rule-group');
        this._createStore('coreshop_customergroups', 'customer-group');
        this._createStore('coreshop_carriers', 'carrier');
        this._createStore('coreshop_pricerules', 'price-rule');
        this._createStore('coreshop_indexes', 'indexes');
        this._createStore('coreshop_product_filters', 'filter');
        this._createStore('coreshop_manufacturers', 'manufacturer');
        this._createStore('coreshop_states', 'state');
        this._createStore('coreshop_messaging_contacts', 'messaging-contact');
        this._createStore('coreshop_messaging_thread_states', 'messaging-thread-state');
        this._createStore('coreshop_shops', 'shop');
        this._createStore('coreshop_carrier_shipping_rules', 'carrier-shipping-rule');

        pimcore.globalmanager.get('coreshop_taxes').load();
        pimcore.globalmanager.get('coreshop_countries').load();
        pimcore.globalmanager.get('coreshop_states').load();
        pimcore.globalmanager.get('coreshop_zones').load();
        pimcore.globalmanager.get('coreshop_currencies').load();
        pimcore.globalmanager.get('coreshop_orderstates').load();
        pimcore.globalmanager.get('coreshop_shops').load();
    },

    _createStore : function (name, url, fields) {
        var proxy = new Ext.data.HttpProxy({
            url : '/plugin/CoreShop/admin_' + url + '/list'
        });

        if (!fields) {
            fields = [
                { name:'id' },
                { name:'name' }
            ];
        }

        var reader = new Ext.data.JsonReader({}, fields);

        var store = new Ext.data.Store({
            restful:    false,
            proxy:      proxy,
            reader:     reader,
            autoload:   true
        });

        pimcore.globalmanager.add(name, store);
    }
};

if (!String.prototype.format) {
    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] != 'undefined'
                ? args[number]
                : match
                ;
        });
    };
}
