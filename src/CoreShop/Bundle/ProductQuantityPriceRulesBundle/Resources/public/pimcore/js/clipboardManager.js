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
