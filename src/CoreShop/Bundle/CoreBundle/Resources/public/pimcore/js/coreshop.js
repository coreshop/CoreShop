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
pimcore.registerNS('coreshop.settings');

coreshop.plugin = Class.create(pimcore.plugin.admin, {
    settings: {},

    getClassName: function () {
        return 'pimcore.plugin.coreshop';
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    uninstall: function () {
        //TODO remove from menu
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

        coreShopMenuItems.push({
            text: t('coreshop_order_by_number'),
            iconCls: 'pimcore_icon_open_object_by_id',
            handler: coreshop.helpers.openOrderByNumberDialog.bind(this)
        });

        if (user.isAllowed('coreshop_permission_settings')) {
            coreShopMenuItems.push({
                text: t('coreshop_settings'),
                iconCls: 'coreshop_icon_settings',
                handler: this.openSettings
            });
        }

        var priceRulesMenu = [];

        if (user.isAllowed('coreshop_permission_price_rules')) {
            priceRulesMenu.push({
                text: t('coreshop_cart_pricerules'),
                iconCls: 'coreshop_icon_price_rule',
                handler: this.openPriceRules
            });
        }

        if (user.isAllowed('coreshop_permission_product_price_rules')) {
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

        if (user.isAllowed('coreshop_permission_countries')) {
            localizationMenu.push({
                text: t('coreshop_countries'),
                iconCls: 'coreshop_icon_country',
                handler: this.openCountryList
            });
        }

        if (user.isAllowed('coreshop_permission_states')) {
            localizationMenu.push({
                text: t('coreshop_states'),
                iconCls: 'coreshop_icon_state',
                handler: this.openStateList
            });
        }

        if (user.isAllowed('coreshop_permission_currencies')) {
            localizationMenu.push({
                text: t('coreshop_currencies'),
                iconCls: 'coreshop_icon_currency',
                handler: this.openCurrencyList
            });
        }

        if (user.isAllowed('coreshop_permission_zones')) {
            localizationMenu.push({
                text: t('coreshop_zones'),
                iconCls: 'coreshop_icon_zone',
                handler: this.openZoneList
            });
        }

        if (user.isAllowed('coreshop_permission_taxes')) {
            localizationMenu.push({
                text: t('coreshop_taxes'),
                iconCls: 'coreshop_icon_taxes',
                handler: this.openTaxes
            });
        }

        if (user.isAllowed('coreshop_permission_tax_rules')) {
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

        ordersMenu.push({
            text: t('coreshop_orders'),
            iconCls: 'coreshop_icon_orders',
            handler: this.openOrders
        });

        ordersMenu.push({
            text: t('coreshop_order_create'),
            iconCls: 'coreshop_icon_order_create',
            handler: function () {
                coreshop.helpers.createOrder();
            }.bind(this)
        });

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

        if (user.isAllowed('coreshop_permission_carriers')) {
            coreShopMenuItems.push({
                text: t('coreshop_shipping'),
                iconCls: 'coreshop_icon_shipping',
                hideOnClick: false,
                menu: {
                    shadow: false,
                    cls: 'pimcore_navigation_flyout',
                    items: [{
                        text: t('coreshop_carriers'),
                        iconCls: 'coreshop_icon_carriers',
                        handler: this.openCarriersList
                    }, {
                        text: t('coreshop_carriers_shipping_rules'),
                        iconCls: 'coreshop_icon_carrier_shipping_rule',
                        handler: this.openCarriersShippingRules
                    }]
                }
            });
        }

        var productsMenu = [];

        productsMenu.push({
            text: t('coreshop_product_list'),
            iconCls: 'coreshop_icon_product_list',
            handler: this.openProducts
        });

        if (user.isAllowed('coreshop_permission_filters')) {
            productsMenu.push({
                text: t('coreshop_product_filters'),
                iconCls: 'coreshop_icon_product_filters',
                handler: this.openProductFilters
            });
        }

        if (user.isAllowed('coreshop_permission_indexes')) {
            productsMenu.push({
                text: t('coreshop_indexes'),
                iconCls: 'coreshop_icon_indexes',
                handler: this.openIndexes
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

        if (user.admin) {

            coreShopMenuItems.push({
                text: t('coreshop_notification_rules'),
                iconCls: 'coreshop_icon_notification_rule',
                handler: this.openNotificationRules
            });

            coreShopMenuItems.push({
                text: t('coreshop_payment_providers'),
                iconCls: 'coreshop_icon_payment_provider',
                handler: this.openPaymentProviders
            });

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

        coreshop.global.initialize(this.settings);

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
        if (tab.data.general.o_className == coreshop.settings.classMapping.cart) {
            tab.toolbar.insert(tab.toolbar.items.length,
                '-'
            );
            tab.toolbar.insert(tab.toolbar.items.length,
                {
                    text: t('coreshop_cart_create_order'),
                    scale: 'medium',
                    iconCls: 'coreshop_icon_create_order',
                    handler: function () {
                        alert('Create Order from Cart');
                    }
                }
            );
        } else if (tab.data.general.o_className == coreshop.settings.classMapping.product) {

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
        } else if (tab.data.general.o_className === coreshop.settings.classMapping.order) {
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
        } else if (tab.data.general.o_className === coreshop.settings.classMapping.order_invoice) {
            var resetChangesFunction = tab.resetChanges;

            var renderTab = new coreshop.invoice.render(tab);

            tab.tabbar.add(renderTab.getLayout());

            tab.resetChanges = function () {
                resetChangesFunction.call(tab);

                renderTab.reload();
            };
        } else if (tab.data.general.o_className === coreshop.settings.classMapping.order_shipment) {
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
            pimcore.globalmanager.add('coreshop_settings', new coreshop.settings());
        }
    },

    openPriceRules: function () {
        try {
            pimcore.globalmanager.get('coreshop_price_rules_panel').activate();
        }
        catch (e) {
            //console.log(e);
            pimcore.globalmanager.add('coreshop_price_rules_panel', new coreshop.cart.pricerules.panel());
        }
    },

    openCurrencyList: function () {
        try {
            pimcore.globalmanager.get('coreshop_currencies_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_currencies_panel', new coreshop.currency.panel());
        }
    },

    openZoneList: function () {
        try {
            pimcore.globalmanager.get('coreshop_zones_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_zones_panel', new coreshop.zone.panel());
        }
    },

    openCountryList: function () {
        try {
            pimcore.globalmanager.get('coreshop_countries_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_countries_panel', new coreshop.country.panel());
        }
    },

    openCarriersList: function () {
        try {
            pimcore.globalmanager.get('coreshop_carriers_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_carriers_panel', new coreshop.carrier.panel());
        }
    },

    openTaxes: function () {
        try {
            pimcore.globalmanager.get('coreshop_taxes_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_taxes_panel', new coreshop.tax.panel());
        }
    },

    openTaxRuleGroups: function () {
        try {
            pimcore.globalmanager.get('coreshop_tax_rule_groups_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_tax_rule_groups_panel', new coreshop.taxrulegroup.panel());
        }
    },

    openOrders: function () {
        try {
            pimcore.globalmanager.get('coreshop_order').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_order', new coreshop.order.order.list());
        }
    },

    openIndexes: function () {
        try {
            pimcore.globalmanager.get('coreshop_indexes_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_indexes_panel', new coreshop.index.panel());
        }
    },

    openProductFilters: function () {
        try {
            pimcore.globalmanager.get('coreshop_product_filters_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_product_filters_panel', new coreshop.filter.panel());
        }
    },

    openStateList: function () {
        try {
            pimcore.globalmanager.get('coreshop_states_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_states_panel', new coreshop.state.panel());
        }
    },

    openProducts: function () {
        try {
            pimcore.globalmanager.get('coreshop_products').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_products', new coreshop.product.grid());
        }
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
        try {
            pimcore.globalmanager.get('coreshop_product_price_rule_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_product_price_rule_panel', new coreshop.product.pricerule.panel());
        }
    },

    openStores: function () {
        try {
            pimcore.globalmanager.get('coreshop_stores_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_stores_panel', new coreshop.store.panel());
        }
    },

    openCarriersShippingRules: function () {
        try {
            pimcore.globalmanager.get('coreshop_carrier_shipping_rule_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_carrier_shipping_rule_panel', new coreshop.shippingrule.panel());
        }
    },

    openNotificationRules: function () {
        try {
            pimcore.globalmanager.get('coreshop_notification_rule_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_notification_rule_panel', new coreshop.notification.rule.panel());
        }
    },

    openPaymentProviders: function () {
        try {
            pimcore.globalmanager.get('coreshop_payment_providers_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_payment_providers_panel', new coreshop.provider.panel());
        }
    }
});

new coreshop.plugin();
