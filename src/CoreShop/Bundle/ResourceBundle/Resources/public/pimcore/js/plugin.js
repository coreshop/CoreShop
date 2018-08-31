/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS("coreshop.resource");
pimcore.registerNS("coreshop.resource.plugin");
coreshop.resource.plugin = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "coreshop.resource.plugin";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
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
