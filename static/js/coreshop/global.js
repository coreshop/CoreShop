/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

pimcore.registerNS("pimcore.plugin.coreshop.global");
pimcore.plugin.coreshop.global = {

    initialize : function(){
        this._initCountries();
        this._initCurrencies();
        this._initCarriers();
        this._initZones();
    },

    _initCurrencies : function() {
        var currencyProxy = new Ext.data.HttpProxy({
            url:'/plugin/CoreShop/admin_currency/get'
        });
        var currencyReader = new Ext.data.JsonReader({
            totalProperty:'total',
            successProperty:'success'
        }, [
            {name:'id'},
            {name:'name'},
            {name:'symbol'},
            {name:'isoCode'},
            {name:'numericIsoCode'},
            {name:'exchangeRate'}
        ]);

        var currencyStore = new Ext.data.Store({
            restful:false,
            proxy:currencyProxy,
            reader:currencyReader
        });
        currencyStore.load();

        pimcore.globalmanager.add("coreshop_currencies", currencyStore);
    },

    _initZones : function() {
        var zoneProxy = new Ext.data.HttpProxy({
            url:'/plugin/CoreShop/admin_zone/get'
        });
        var zoneReader = new Ext.data.JsonReader({
            totalProperty:'total',
            successProperty:'success'
        }, [
            {name:'id'},
            {name:'name'},
            {name:'active'}
        ]);

        var zoneStore = new Ext.data.Store({
            restful:false,
            proxy:zoneProxy,
            reader:zoneReader
        });
        zoneStore.load();

        pimcore.globalmanager.add("coreshop_zones", zoneStore);
    },

    _initCountries : function() {
        var countryProxy = new Ext.data.HttpProxy({
            url:'/plugin/CoreShop/admin_country/get-countries'
        });
        var countryReader = new Ext.data.JsonReader({
            totalProperty:'total',
            successProperty:'success'
        }, [
            {name:'id'},
            {name:'name'},
            {name:'symbol'},
            {name:'isoCode'},
            {name:'numericIsoCode'},
            {name:'exchangeRate'}
        ]);

        var countryStore = new Ext.data.Store({
            restful:false,
            proxy:countryProxy,
            reader:countryReader
        });
        countryStore.load();

        pimcore.globalmanager.add("coreshop_countries", countryStore);
    },

    _initCarriers : function() {
        var carrierProxy  = new Ext.data.HttpProxy({
            url:'/plugin/CoreShop/admin_country/get-countries'
        });
        var carrierReader = new Ext.data.JsonReader({
            totalProperty:'total',
            successProperty:'success'
        }, [
            {name:'id'},
            {name:'name'}
        ]);

        var carrierStore = new Ext.data.Store({
            restful:false,
            proxy:carrierProxy,
            reader:carrierReader
        });
        carrierStore.load();

        pimcore.globalmanager.add("coreshop_carriers", carrierStore);
    }
};