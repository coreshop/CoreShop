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

pimcore.registerNS('coreshop.global');
coreshop.global = {
    resource: null,

    addStore: function (name, url, fields, sorters) {
        return this.addStoreWithUrl(name, '/admin/' + url + '/list', fields, sorters);
    },

    addStoreWithRoute: function (name, route, fields, sorters) {
        return this.addStoreWithUrl(name, Routing.generate(route), fields, sorters);
    },

    addStoreWithUrl: function(name, url, fields, sorters) {
        if (!fields) {
            fields = [
                {name: 'id'},
                {name: 'name'}
            ];
        }

        if (Ext.isDefined('Ext.CoreShop.Store.' + name)) {
            Ext.define('Ext.CoreShop.Store.' + name, {
                extend: 'Ext.data.Store',
                alias: 'store.' + name,
                proxy: {
                    type: 'ajax',
                    url: url,
                    reader: {
                        type: 'json',
                    }
                },
                fields: fields,
                autoLoad: true,
                sorters: sorters ? sorters : [],
                remoteSort: false,
                remoteFilter: false
            });
        }

        var store = new Ext.CoreShop.Store[name]({
            autoLoad: false
        });

        pimcore.globalmanager.add(name, store);
    },
};


String.prototype.ucfirst = function () {
    return this.charAt(0).toUpperCase() + this.slice(1).toLowerCase();
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
