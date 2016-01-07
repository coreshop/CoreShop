/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS("pimcore.plugin.coreshop");

pimcore.plugin.coreshop = Class.create(pimcore.plugin.admin,{


    isInitialized : false,
    settings : {},

    getClassName: function (){
        return "pimcore.plugin.coreshop";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },

    uninstall: function(){
        //TODO remove from menu
    },

    pimcoreReady: function (params, broker) {
        var self = this;
        var coreShopMenuItems = [];
        var statusbar = pimcore.globalmanager.get("statusbar");
        var toolbar = pimcore.globalmanager.get("layout_toolbar");
        var coreShopStatusItem = statusbar.insert(0, '<em class="fa fa-spinner"></em> ' + t("coreshop_loading"));
        statusbar.insert(1, "-");

        pimcore.globalmanager.add("coreshop_statusbar_item", coreShopStatusItem);

        pimcore.plugin.coreshop.broker.addListener("storesLoaded", function() {
            this.isInitialized = true;

            coreShopStatusItem.setHtml('<em class="fa fa-shopping-cart"></em> ' + t("coreshop_loaded").format(this.settings.plugin.pluginVersion))
        }, this);

        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_settings/get-settings",
            success: function (response)
            {
                var resp = Ext.decode(response.responseText);

                this.settings = resp;

                if(intval(this.settings.coreshop.isInstalled)) {
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
                        text: t("coreshop_order"),
                        iconCls: "coreshop_icon_order",
                        hideOnClick: false,
                        menu: {
                            cls: "pimcore_navigation_flyout",
                            shadow: false,
                            items: [{
                                text: t("coreshop_order_states"),
                                iconCls: "coreshop_icon_order_states",
                                handler: this.openOrderStates
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

                Ext.get('pimcore_menu_logout').insertSibling('<li id="pimcore_menu_coreshop" class="pimcore_menu_item icon-coreshop-shop">'+t("coreshop")+'</li>');
                Ext.get("pimcore_menu_coreshop").on("mousedown", function(e, el) {
                    if(!self.isInitialized) {
                        Ext.Msg.alert(t('coreshop'), t('coreshop_not_initialized'));
                    }
                    else {
                        toolbar.showSubMenu.call(menu, e, el);
                    }
                });

                pimcore.plugin.coreshop.global.initialize(this.settings);

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
    },

    openOrderStates : function() {
        try {
            pimcore.globalmanager.get("coreshop_order_states").activate();
        }
        catch (e) {
            pimcore.globalmanager.add("coreshop_order_states", new pimcore.plugin.coreshop.orderstate.panel());
        }
    }
});

new pimcore.plugin.coreshop();