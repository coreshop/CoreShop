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

pimcore.registerNS("coreshop.resource");
pimcore.registerNS("coreshop.resource.plugin");
coreshop.resource.plugin = Class.create({
    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, (e) => {
            this.pimcoreReady(e.detail.params, e.detail.broker);
        });
        document.addEventListener(pimcore.events.postOpenObject, (e) => {
            this.postOpenObject(e.detail.object, e.detail.type);
        });
        document.addEventListener(pimcore.events.preOpenObject, (e) => {
            this.preOpenObject(e.detail.object, e.detail.type);
        });
        document.addEventListener(pimcore.events.postOpenAsset, (e) => {
            this.postOpenAsset(e.detail.object, e.detail.type);
        });
        document.addEventListener(pimcore.events.preOpenAsset, (e) => {
            this.preOpenAsset(e.detail.object, e.detail.type);
        });
        document.addEventListener(pimcore.events.postOpenDocument, (e) => {
            this.postOpenDocument(e.detail.object, e.detail.type);
        });
        document.addEventListener(pimcore.events.preOpenDocument, (e) => {
            this.preOpenDocument(e.detail.object, e.detail.type);
        });
    },

    uninstall: function () {
    },

    pimcoreReady: function (params, broker) {
        this.fire('pimcore.ready', arguments);
    },

    preOpenObject: function (object, type) {
        this.fire('pimcore.preOpenObject', arguments);
    },

    postOpenObject: function (object, type) {
        this.fire('pimcore.postOpenObject', arguments);
    },

    preOpenAsset: function (asset, type) {
        this.fire('pimcore.preOpenAsset', arguments);
    },

    postOpenAsset: function (asset, type) {
        this.fire('pimcore.postOpenAsset', arguments);
    },

    preOpenDocument: function (document, type) {
        this.fire('pimcore.preOpenDocument', arguments);
    },

    postOpenDocument: function (document, type) {
        this.fire('pimcore.postOpenDocument', arguments);
    },

    fire: function(event, args) {
        args = Ext.Object.getValues(args);
        args.unshift(event);

        coreshop.broker.fireEvent.apply(this, args);
    }
});

new coreshop.resource.plugin();
