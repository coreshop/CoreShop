/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

pimcore.registerNS("pimcore.plugin.coreshop");

pimcore.plugin.coreshop = Class.create(pimcore.plugin.admin,{


    getClassName: function (){
        return "pimcore.plugin.coreshop";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },

    uninstall: function(){
        //TODO remove from menu
    },

    pimcoreReady: function (params, broker){
        var coreShopMenuItems = [];

        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_settings/installed",
            success: function (response)
            {
                var resp = Ext.decode(response.responseText);

                if(resp.isInstalled) {
                    pimcore.plugin.coreshop.global.initialize();

                    coreShopMenuItems.push({
                        text: t("coreshop_settings"),
                        iconCls: "coreshop_icon_settings",
                        handler: this.openSettings
                    });

                    coreShopMenuItems.push({
                        text: t("coreshop_price_rules"),
                        iconCls: "coreshop_icon_price_rule",
                        handler: this.openPriceRules
                    });

                    coreShopMenuItems.push({
                        text: t("coreshop_localization"),
                        iconCls: "coreshop_icon_localization",
                        hideOnClick: false,
                        menu: {
                            cls: "pimcore_navigation_flyout",
                            shadow: false,
                            items: [{
                                text: t("coreshop_countries"),
                                iconCls: "coreshop_icon_country",
                                handler: this.openCountryList
                            }, {
                                text: t("coreshop_currencies"),
                                iconCls: "coreshop_icon_currency",
                                handler: this.openCurrencyList
                            }, {
                                text: t("coreshop_zones"),
                                iconCls: "coreshop_icon_zone",
                                handler: this.openZoneList
                            }]
                        }
                    });

                    coreShopMenuItems.push({
                        text: t("coreshop_shipping"),
                        iconCls: "coreshop_icon_shipping",
                        hideOnClick: false,
                        menu: {
                            shadow: false,
                            cls: "pimcore_navigation_flyout",
                            items: [{
                                text: t("coreshop_carriers"),
                                iconCls: "coreshop_icon_carriers",
                                handler: this.openCarriersList
                            }]
                        }
                    });
                }
                else {
                    coreShopMenuItems.push({
                        text: t("coreshop_install"),
                        iconCls: "coreshop_icon_setup",
                        handler: this.openSetup
                    });
                }


                var menu = new Ext.menu.Menu({
                    items: coreShopMenuItems,
                    shadow: false,
                    cls: "pimcore_navigation_flyout"
                });

                Ext.get('pimcore_menu_logout').insertSibling('<li id="pimcore_menu_coreshop" class="pimcore_menu_item icon-coreshop-shop">CoreShop</li>');

                var toolbar = pimcore.globalmanager.get("layout_toolbar");
                Ext.get("pimcore_menu_coreshop").on("mousedown", toolbar.showSubMenu.bind(menu));

            }.bind(this)
        });
    },

    postOpenObject : function(tab, type)
    {
        if(tab.data.general.o_className == "CoreShopCart")
        {
            tab.toolbar.insert(tab.toolbar.items.length,
                '-'
            );
            tab.toolbar.insert(tab.toolbar.items.length,
                {
                    text: t("coreshop_cart_create_order"),
                    scale: "medium",
                    iconCls: "coreshop_icon_create_order",
                    handler: function() {
                        alert("Create Order from Cart");
                    }
                }
            );
        }
    },

    openSettings : function()
    {
        try {
            pimcore.globalmanager.get("coreshop_settings").activate();
        }
        catch (e) {
            //console.log(e);
            pimcore.globalmanager.add("coreshop_settings", new pimcore.plugin.coreshop.settings());
        }
    },

    openPriceRules : function()
    {
        try {
            pimcore.globalmanager.get("coreshop_price_rules").activate();
        }
        catch (e) {
            //console.log(e);
            pimcore.globalmanager.add("coreshop_price_rules", new pimcore.plugin.coreshop.pricerule.panel());
        }
    },

    openCurrencyList : function() {
        try {
            pimcore.globalmanager.get("coreshop_currency").activate();
        }
        catch (e) {
            pimcore.globalmanager.add("coreshop_currency", new pimcore.plugin.coreshop.currencies.panel());
        }
    },

    openZoneList : function() {
        try {
            pimcore.globalmanager.get("coreshop_zones").activate();
        }
        catch (e) {
            pimcore.globalmanager.add("coreshop_zones", new pimcore.plugin.coreshop.zones.panel());
        }
    },

    openCountryList : function() {
        try {
            pimcore.globalmanager.get("coreshop_country").activate();
        }
        catch (e) {
            pimcore.globalmanager.add("coreshop_country", new pimcore.plugin.coreshop.countries.panel());
        }
    },

    openCarriersList : function() {
        try {
            pimcore.globalmanager.get("coreshop_carriers").activate();
        }
        catch (e) {
            pimcore.globalmanager.add("coreshop_carriers", new pimcore.plugin.coreshop.carriers.panel());
        }
    },

    openSetup : function() {
        try {
            pimcore.globalmanager.get("coreshop_install").activate();
        }
        catch (e) {
            pimcore.globalmanager.add("coreshop_install", new pimcore.plugin.coreshop.install());
        }
    }
});

new pimcore.plugin.coreshop();