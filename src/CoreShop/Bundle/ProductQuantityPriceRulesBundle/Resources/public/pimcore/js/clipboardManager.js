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

pimcore.registerNS('coreshop.product_quantity_price_rules.clipboardManager');
coreshop.product_quantity_price_rules.clipboardManager = Class.create({

    id: null,
    clipboard: {},
    dispatcher: {},

    initialize: function () {
        this.clipboard = {};
        this.dispatcher = {};
    },

    registerDispatcher: function (id, callback) {
        this.dispatcher[id] = callback;
        return id;
    },

    unRegisterDispatcher: function (id) {
        if (this.dispatcher.hasOwnProperty(id)) {
            delete this.dispatcher[id];
        }
    },

    executeDispatch: function (key, value, type) {
        Ext.Object.each(this.dispatcher, function (id, callback) {
            callback.apply(callback, {key: key, value: value, type: type});
        });
    },

    hasData: function (key) {
        return this.clipboard.hasOwnProperty(key) && this.clipboard[key] !== null;
    },

    addData: function (key, value) {
        this.clipboard[key] = value;
        this.executeDispatch(key, value, 'add');
    },

    getData: function (key) {
        return this.hasData(key) ? this.clipboard[key] : null;
    },

    removeData: function (key) {
        if (this.clipboard.hasOwnProperty(key)) {
            delete this.clipboard[key];
            this.executeDispatch(key, null, 'remove');
        }
    },

    clear: function () {
        this.clipboard = {};
        this.dispatcher = {};
    },
});
