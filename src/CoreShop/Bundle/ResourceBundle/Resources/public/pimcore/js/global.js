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

pimcore.registerNS('coreshop.global');
coreshop.global = {
    resource: null,

    addStore: function (name, url, fields, sorters) {
        var proxy = new Ext.data.HttpProxy({
            url: '/admin/' + url + '/list'
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
            autoload: true,
            fields: fields,
            sorters: sorters ? sorters : [],
            remoteSort: false,
            remoteFilter: false
        });

        pimcore.globalmanager.add(name, store);
    }
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
