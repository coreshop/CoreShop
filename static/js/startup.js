pimcore.registerNS("pimcore.plugin.coreshop");

pimcore.plugin.coreshop = Class.create(pimcore.plugin.admin, {
    getClassName: function() {
        return "pimcore.plugin.coreshop";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },
 
    pimcoreReady: function (params,broker){
        // alert("Example Ready!");
    }
});

var coreshopPlugin = new pimcore.plugin.coreshop();

