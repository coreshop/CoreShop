pimcore.registerNS("pimcore.plugin.ElasticsearchBundle");

pimcore.plugin.ElasticsearchBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.ElasticsearchBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("ElasticsearchBundle ready!");
    }
});

var ElasticsearchBundlePlugin = new pimcore.plugin.ElasticsearchBundle();
