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

pimcore.registerNS('coreshop.plugin');
coreshop.plugin = Class.create(pimcore.plugin.admin, {
    settings: {},

    getClassName: function () {
        return 'pimcore.plugin.coreshop';
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    uninstall: function () {
        //Nothing to do here, reload pimcore
    },

    pimcoreReady: function (params, broker) {
        Ext.get('pimcore_status').insertHtml('beforeEnd', '<div id="coreshop_status" class="loading" data-menu-tooltip="' + t('coreshop_loading') + '"></div>');

        Ext.Ajax.request({
            url: '/admin/coreshop/settings/get-settings',
            success: function (response) {
                resp = Ext.decode(response.responseText);

                this.settings = resp;
                coreshop.settings = this.settings;

                this.initializeCoreShop();
            }.bind(this)
        });
    },

    initializeCoreShop: function () {
        var self = this;
        var coreShopMenuItems = [];
        var user = pimcore.globalmanager.get('user');

        var toolbar = pimcore.globalmanager.get('layout_toolbar');

        if (user.isAllowed('coreshop_permission_order_detail')) {
            coreShopMenuItems.push({
                text: t('coreshop_order_by_number'),
                iconCls: 'coreshop_icon_order',
                handler: coreshop.order.helper.openSaleByNumberDialog.bind(this, 'order')
            });
        }

        if (user.isAllowed('coreshop_permission_quote_detail')) {
            coreShopMenuItems.push({
                text: t('coreshop_quote_by_number'),
                iconCls: 'coreshop_icon_quote',
                handler: coreshop.order.helper.openSaleByNumberDialog.bind(this, 'quote')
            });
        }

        if (user.isAllowed('coreshop_permission_settings')) {
            coreShopMenuItems.push({
                text: t('coreshop_settings'),
                iconCls: 'coreshop_icon_settings',
                handler: this.openSettings
            });
        }

        var priceRulesMenu = [];

        if (user.isAllowed('coreshop_permission_cart_price_rule')) {
            priceRulesMenu.push({
                text: t('coreshop_cart_pricerules'),
                iconCls: 'coreshop_icon_price_rule',
                handler: this.openPriceRules
            });
        }

        if (user.isAllowed('coreshop_permission_product_price_rule')) {
            priceRulesMenu.push({
                text: t('coreshop_product_pricerules'),
                iconCls: 'coreshop_icon_price_rule',
                handler: this.openProductPriceRules
            });
        }

        if (priceRulesMenu.length > 0) {
            coreShopMenuItems.push({
                text: t('coreshop_pricerules'),
                iconCls: 'coreshop_icon_price_rule',
                hideOnClick: false,
                menu: {
                    cls: 'pimcore_navigation_flyout',
                    shadow: false,
                    items: priceRulesMenu
                }
            });
        }

        var localizationMenu = [];

        if (user.isAllowed('coreshop_permission_country')) {
            localizationMenu.push({
                text: t('coreshop_countries'),
                iconCls: 'coreshop_icon_country',
                handler: this.openCountryList
            });
        }

        if (user.isAllowed('coreshop_permission_state')) {
            localizationMenu.push({
                text: t('coreshop_states'),
                iconCls: 'coreshop_icon_state',
                handler: this.openStateList
            });
        }

        if (user.isAllowed('coreshop_permission_currency')) {
            localizationMenu.push({
                text: t('coreshop_currencies'),
                iconCls: 'coreshop_icon_currency',
                handler: this.openCurrencyList
            });
        }

        if (user.isAllowed('coreshop_permission_exchange_rate')) {
            localizationMenu.push({
                text: t('coreshop_exchange_rates'),
                iconCls: 'coreshop_icon_exchange_rate',
                handler: this.openExchangeRates
            });
        }

        if (user.isAllowed('coreshop_permission_zone')) {
            localizationMenu.push({
                text: t('coreshop_zones'),
                iconCls: 'coreshop_icon_zone',
                handler: this.openZoneList
            });
        }

        if (user.isAllowed('coreshop_permission_tax_item')) {
            localizationMenu.push({
                text: t('coreshop_taxes'),
                iconCls: 'coreshop_icon_taxes',
                handler: this.openTaxes
            });
        }

        if (user.isAllowed('coreshop_permission_tax_rule_group')) {
            localizationMenu.push({
                text: t('coreshop_taxrulegroups'),
                iconCls: 'coreshop_icon_tax_rule_groups',
                handler: this.openTaxRuleGroups
            });
        }

        if (localizationMenu.length > 0) {
            coreShopMenuItems.push({
                text: t('coreshop_localization'),
                iconCls: 'coreshop_icon_localization',
                hideOnClick: false,
                menu: {
                    cls: 'pimcore_navigation_flyout',
                    shadow: false,
                    items: localizationMenu
                }
            });
        }

        var ordersMenu = [];

        if (user.isAllowed('coreshop_permission_order_list')) {
            ordersMenu.push({
                text: t('coreshop_orders'),
                iconCls: 'coreshop_icon_orders',
                handler: this.openOrders
            });
        }

        /*if (user.isAllowed('coreshop_permission_order_create')) {
            ordersMenu.push({
                text: t('coreshop_order_create'),
                iconCls: 'coreshop_icon_order_create',
                handler: function () {
                    coreshop.helpers.createOrder();
                }.bind(this)
            });
        }*/

        if (user.isAllowed('coreshop_permission_quote_list')) {
            ordersMenu.push({
                text: t('coreshop_quotes'),
                iconCls: 'coreshop_icon_quotes',
                handler: this.openQuotes
            });
        }

        /*if (user.isAllowed('coreshop_permission_quote_create')) {
            ordersMenu.push({
                text: t('coreshop_quote_create'),
                iconCls: 'coreshop_icon_quote_create',
                handler: function () {
                    coreshop.helpers.createQuote();
                }.bind(this)
            });
        }*/

        if (ordersMenu.length > 0) {
            coreShopMenuItems.push({
                text: t('coreshop_order'),
                iconCls: 'coreshop_icon_order',
                hideOnClick: false,
                menu: {
                    cls: 'pimcore_navigation_flyout',
                    shadow: false,
                    items: ordersMenu
                }
            });
        }

        var carriersMenu = [];

        if (user.isAllowed('coreshop_permission_carrier')) {
            carriersMenu.push({
                text: t('coreshop_carriers'),
                iconCls: 'coreshop_icon_carriers',
                handler: this.openCarriersList
            });
        }

        if (user.isAllowed('coreshop_permission_shipping_rule')) {
            carriersMenu.push({
                text: t('coreshop_carriers_shipping_rules'),
                iconCls: 'coreshop_icon_carrier_shipping_rule',
                handler: this.openCarriersShippingRules
            });
        }

        if (carriersMenu.length > 0) {
            coreShopMenuItems.push({
                text: t('coreshop_shipping'),
                iconCls: 'coreshop_icon_shipping',
                hideOnClick: false,
                menu: {
                    shadow: false,
                    cls: 'pimcore_navigation_flyout',
                    items: carriersMenu
                }
            });
        }

        var productsMenu = [];

        if (user.classes.indexOf(coreshop.class_map.product) >= 0) {
            productsMenu.push({
                text: t('coreshop_product_list'),
                iconCls: 'coreshop_icon_product_list',
                handler: this.openProducts
            });
        }


        if (user.isAllowed('coreshop_permission_index')) {
            productsMenu.push({
                text: t('coreshop_indexes'),
                iconCls: 'coreshop_icon_indexes',
                handler: this.openIndexes
            });
        }

        if (user.isAllowed('coreshop_permission_filter')) {
            productsMenu.push({
                text: t('coreshop_filters'),
                iconCls: 'coreshop_icon_filters',
                handler: this.openProductFilters
            });
        }

        if (productsMenu.length > 0) {
            coreShopMenuItems.push({
                text: t('coreshop_product'),
                iconCls: 'coreshop_icon_product',
                hideOnClick: false,
                menu: {
                    cls: 'pimcore_navigation_flyout',
                    shadow: false,
                    items: productsMenu
                }
            });
        }

        /*var messagingMenu = [];

         if (user.isAllowed('coreshop_permission_messaging_thread')) {
         messagingMenu.push({
         text: t('coreshop_messaging_thread'),
         iconCls: 'coreshop_icon_messaging_thread',
         handler: this.openMessagingThread
         });
         }

         if (user.isAllowed('coreshop_permission_messaging_contact')) {
         messagingMenu.push({
         text: t('coreshop_messaging_contact'),
         iconCls: 'coreshop_icon_messaging_contact',
         handler: this.openMessagingContact
         });
         }

         if (user.isAllowed('coreshop_permission_messaging_thread_state')) {
         messagingMenu.push({
         text: t('coreshop_messaging_threadstate'),
         iconCls: 'coreshop_icon_messaging_thread_state',
         handler: this.openMessagingThreadState
         });
         }

         if (messagingMenu.length > 0) {
         coreShopMenuItems.push({
         text: t('coreshop_messaging'),
         iconCls: 'coreshop_icon_messaging',
         hideOnClick: false,
         menu: {
         cls: 'pimcore_navigation_flyout',
         shadow: false,
         items: messagingMenu
         }
         });
         }*/

        if (user.isAllowed('coreshop_permission_notification')) {
            coreShopMenuItems.push({
                text: t('coreshop_notification_rules'),
                iconCls: 'coreshop_icon_notification_rule',
                handler: this.openNotificationRules
            });
        }

        if (user.isAllowed('coreshop_permission_payment_provider')) {
            coreShopMenuItems.push({
                text: t('coreshop_payment_providers'),
                iconCls: 'coreshop_icon_payment_provider',
                handler: this.openPaymentProviders
            });
        }

        if (user.isAllowed('coreshop_permission_store')) {
            coreShopMenuItems.push({
                text: t('coreshop_stores'),
                iconCls: 'coreshop_icon_store',
                handler: this.openStores
            });
        }


        coreShopMenuItems.push({
            text: 'ABOUT CoreShop &reg;',
            iconCls: 'coreshop_icon_logo',
            handler: function () {
                coreshop.helpers.showAbout();
            }
        });

        if (coreShopMenuItems.length > 0) {
            this._menu = new Ext.menu.Menu({
                items: coreShopMenuItems,
                shadow: false,
                cls: 'pimcore_navigation_flyout'
            });

            Ext.get('pimcore_navigation').down('ul').insertHtml('beforeEnd', '<li id="pimcore_menu_coreshop" data-menu-tooltip="' + t('coreshop') + '" class="pimcore_menu_item pimcore_menu_needs_children"></li>');
            Ext.get('pimcore_menu_coreshop').on('mousedown', function (e, el) {
                toolbar.showSubMenu.call(this._menu, e, el);
            }.bind(this));
        }

        Ext.get('coreshop_status').set(
            {
                'data-menu-tooltip': t('coreshop_loaded').format('2.0 ALPHA'), //TODO: VERSION
                class: ''
            }
        );

        $('[data-menu-tooltip]').unbind('mouseenter');
        $('[data-menu-tooltip]').unbind('mouseleave');

        $('[data-menu-tooltip]').mouseenter(function (e) {
            $('#pimcore_menu_tooltip').show();
            $('#pimcore_menu_tooltip').html($(this).data('menu-tooltip'));

            var offset = $(e.target).offset();
            var top = offset.top;
            top = top + ($(e.target).height() / 2);

            $('#pimcore_menu_tooltip').css({top: top});
        });

        $('[data-menu-tooltip]').mouseleave(function () {
            $('#pimcore_menu_tooltip').hide();
        });

        $(document).trigger('coreShopReady');

        //coreshop.plugin.broker.fireEvent('coreshopReady', this);

        //Add Report Definition
        pimcore.report.broker.addGroup('coreshop', 'coreshop_reports', 'coreshop_icon_report');
        pimcore.report.broker.addGroup('coreshop_monitoring', 'coreshop_monitoring', 'coreshop_icon_monitoring');

        Ext.Object.each(coreshop.report.reports, function (report) {
            report = coreshop.report.reports[report];

            pimcore.report.broker.addReport(report, 'coreshop', {
                name: report.prototype.getName(),
                text: report.prototype.getName(),
                niceName: report.prototype.getName(),
                iconCls: report.prototype.getIconCls()
            });
        });

        Ext.Object.each(coreshop.report.monitoring.reports, function (report) {
            report = coreshop.report.monitoring.reports[report];

            pimcore.report.broker.addReport(report, 'coreshop_monitoring', {
                name: report.prototype.getName(),
                text: report.prototype.getName(),
                niceName: report.prototype.getName(),
                iconCls: report.prototype.getIconCls()
            });
        });
    },

    addPluginMenu: function (menu) {
        if (!this._pluginsMenu) {
            this._pluginsMenu = this._menu.add({
                text: t('coreshop_plugins'),
                iconCls: 'coreshop_icon_plugins',
                hideOnClick: false,
                menu: {
                    shadow: false,
                    cls: 'pimcore_navigation_flyout',
                    items: []
                }
            });
        }

        this._pluginsMenu.menu.add(menu);
    },

    postOpenObject: function (tab, type) {
        if (tab.data.general.o_className === coreshop.class_map.cart) {
            tab.toolbar.insert(tab.toolbar.items.length,
                '-'
            );
            /*tab.toolbar.insert(tab.toolbar.items.length,
                {
                    text: t('coreshop_cart_create_order'),
                    scale: 'medium',
                    iconCls: 'coreshop_icon_create_order',
                    handler: function () {
                        alert('Create Order from Cart');
                    }
                }
            );*/
        } else if (tab.data.general.o_className === coreshop.class_map.product) {

            tab.toolbar.insert(tab.toolbar.items.length,
                '-'
            );
            tab.toolbar.insert(tab.toolbar.items.length,
                {
                    text: t('coreshop_generate_variants'),
                    scale: 'medium',
                    iconCls: 'pimcore_icon_tab_variants',
                    handler: function () {
                        new coreshop.object.variantGenerator(tab);
                    }.bind(this, tab)
                }
            );

            //tab.tabbar.add(new pimcore.plugin.coreshop.product.specificprice.panel(tab).getLayout());

            /*tab.tab.items.items[0].add({
             text: t('generate_variants'),
             iconCls: 'pimcore_icon_tab_variants',
             scale: 'medium',
             handler: function(obj){
             //new pimcore.plugin.VariantGenerator.VariantGeneratorDialog(obj);
             }.bind(this, tab)
             });*/
        } else if (tab.data.general.o_className === coreshop.class_map.order) {
            var orderMoreButtons = [];

            orderMoreButtons.push(
                {
                    text: t('coreshop_add_payment'),
                    scale: 'medium',
                    iconCls: 'coreshop_icon_currency',
                    handler: function () {
                        coreshop.order.order.createPayment.showWindow(tab.id, tab.data.data, function () {
                            tab.reload(tab.data.currentLayoutId);
                        });
                    }.bind(this, tab)
                }
            );

            orderMoreButtons.push(
                {
                    text: t('coreshop_send_message'),
                    scale: 'medium',
                    iconCls: 'coreshop_icon_messaging_thread',
                    handler: function () {
                        coreshop.order.order.message.showWindow(tab);
                    }.bind(this, tab)
                }
            );

            orderMoreButtons.push({
                text: t('open'),
                scale: 'medium',
                iconCls: 'coreshop_icon_order',
                handler: function () {
                    coreshop.order.helper.openOrder(tab.id);
                }.bind(this, tab)
            });

            if (orderMoreButtons.length > 0) {
                tab.toolbar.insert(tab.toolbar.items.length,
                    '-'
                );

                tab.toolbar.insert(tab.toolbar.items.length,
                    {
                        text: t('coreshop_more'),
                        scale: 'medium',
                        iconCls: 'coreshop_icon_logo',
                        menu: orderMoreButtons
                    }
                );
            }
        } else if (tab.data.general.o_className === coreshop.class_map.order_invoice) {
            var resetChangesFunction = tab.resetChanges;

            var renderTab = new coreshop.invoice.render(tab);

            tab.tabbar.add(renderTab.getLayout());

            tab.resetChanges = function () {
                resetChangesFunction.call(tab);

                renderTab.reload();
            };
        } else if (tab.data.general.o_className === coreshop.class_map.order_shipment) {
            var resetChangesFunction = tab.resetChanges;

            var renderTab = new coreshop.shipment.render(tab);

            tab.tabbar.add(renderTab.getLayout());

            tab.resetChanges = function () {
                resetChangesFunction.call(tab);

                renderTab.reload();
            };
        }

        pimcore.layout.refresh();
    },

    openSettings: function () {
        try {
            pimcore.globalmanager.get('coreshop_settings').activate();
        }
        catch (e) {
            //console.log(e);
            pimcore.globalmanager.add('coreshop_settings', new coreshop.core.settings());
        }
    },

    openCurrencyList: function () {
        coreshop.global.resource.open('coreshop.currency', 'currency');
    },

    openExchangeRates: function () {
        coreshop.global.resource.open('coreshop.currency', 'exchange_rate');
    },

    openZoneList: function () {
        coreshop.global.resource.open('coreshop.address', 'zone');
    },

    openCountryList: function () {
        coreshop.global.resource.open('coreshop.address', 'country');
    },

    openStateList: function () {
        coreshop.global.resource.open('coreshop.address', 'state');
    },

    openCarriersList: function () {
        coreshop.global.resource.open('coreshop.shipping', 'carrier');
    },

    openCarriersShippingRules: function () {
        coreshop.global.resource.open('coreshop.shipping', 'shipping_rules');
    },

    openTaxes: function () {
        coreshop.global.resource.open('coreshop.taxation', 'tax_item');
    },

    openTaxRuleGroups: function () {
        coreshop.global.resource.open('coreshop.taxation', 'tax_rule_group');
    },

    openOrders: function () {
        coreshop.global.resource.open('coreshop.order', 'orders');
    },

    openQuotes: function () {
        coreshop.global.resource.open('coreshop.order', 'quotes');
    },

    openPriceRules: function () {
        coreshop.global.resource.open('coreshop.order', 'cart_price_rule');
    },

    openIndexes: function () {
        coreshop.global.resource.open('coreshop.index', 'index');
    },

    openProductFilters: function () {
        coreshop.global.resource.open('coreshop.index', 'filter');
    },

    openProducts: function () {
        coreshop.global.resource.open('coreshop.product', 'products');
    },

    /*openMessagingContact: function () {
     try {
     pimcore.globalmanager.get('coreshop_messaging_contacts_panel').activate();
     }
     catch (e) {
     pimcore.globalmanager.add('coreshop_messaging_contacts_panel', new pimcore.plugin.coreshop.messaging.contact.panel());
     }
     },

     openMessagingThreadState: function () {
     try {
     pimcore.globalmanager.get('coreshop_messaging_thread_state_panel').activate();
     }
     catch (e) {
     pimcore.globalmanager.add('coreshop_messaging_thread_state_panel', new pimcore.plugin.coreshop.messaging.threadstate.panel());
     }
     },

     openMessagingThread: function () {
     try {
     pimcore.globalmanager.get('coreshop_messaging_thread_panel').activate();
     }
     catch (e) {
     pimcore.globalmanager.add('coreshop_messaging_thread_panel', new pimcore.plugin.coreshop.messaging.thread.panel());
     }
     },*/

    openProductPriceRules: function () {
        coreshop.global.resource.open('coreshop.product', 'product_price_rule');
    },

    openStores: function () {
        coreshop.global.resource.open('coreshop.store', 'store');
    },

    openNotificationRules: function () {
        coreshop.global.resource.open('coreshop.notification', 'notification_rule');
    },

    openPaymentProviders: function () {
        coreshop.global.resource.open('coreshop.payment', 'payment_provider');
    }
});

var plugin = new coreshop.plugin();
