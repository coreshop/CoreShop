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

pimcore.registerNS("coreshop.test");
pimcore.registerNS("coreshop.test.plugin");
coreshop.test.plugin = Class.create({
    getClassName: function () {
        return "coreshop.test.plugin";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    uninstall: function () {

    },

    pimcoreReady: function (params, broker) {
        document.body.classList.add('coreshop_loaded');
    },

    preOpenObject: function (object, type) {

    },

    postOpenObject: function (object, type) {

    },

    preOpenAsset: function (asset, type) {

    },

    postOpenAsset: function (asset, type) {

    },

    preOpenDocument: function (document, type) {

    },

    postOpenDocument: function (document, type) {

    }
});

new coreshop.test.plugin();
