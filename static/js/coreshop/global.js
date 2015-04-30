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
    }
};