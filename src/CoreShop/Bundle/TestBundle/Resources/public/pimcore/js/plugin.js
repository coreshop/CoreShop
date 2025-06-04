/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
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
