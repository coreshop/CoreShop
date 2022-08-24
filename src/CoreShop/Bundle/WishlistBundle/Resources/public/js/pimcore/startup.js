pimcore.registerNS("pimcore.plugin.CoreShopWishlistBundle");

pimcore.plugin.CoreShopWishlistBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.CoreShopWishlistBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("CoreShopWishlistBundle ready!");
    }
});

var CoreShopWishlistBundlePlugin = new pimcore.plugin.CoreShopWishlistBundle();
