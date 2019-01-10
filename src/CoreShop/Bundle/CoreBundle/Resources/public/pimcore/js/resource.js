/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */
pimcore.registerNS('coreshop.core');
pimcore.registerNS('coreshop.core.resource');
coreshop.core.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.broker.addListener('pimcore.ready', this.pimcoreReady, this);
        coreshop.broker.addListener('pimcore.postOpenObject', this.postOpenObject, this);
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

        if (user.isAllowed('coreshop_permission_order_create')) {
            ordersMenu.push({
                text: t('coreshop_order_create'),
                iconCls: 'coreshop_icon_order_create',
                handler: this.openCreateOrder
            });
        }

        if (user.isAllowed('coreshop_permission_quote_list')) {
            ordersMenu.push({
                text: t('coreshop_quotes'),
                iconCls: 'coreshop_icon_quotes',
                handler: this.openQuotes
            });
        }

        if (user.isAllowed('coreshop_permission_quote_create')) {
            ordersMenu.push({
                text: t('coreshop_quote_create'),
                iconCls: 'coreshop_icon_quote_create',
                handler: this.openCreateQuote
            });
        }

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

            Ext.get('pimcore_navigation').down('ul').insertHtml('beforeEnd', '<li id="pimcore_menu_coreshop" data-menu-tooltip="' + t('coreshop') + '" class="pimcore_menu_item pimcore_menu_needs_children">' +
                '<svg version="1.1" id="Ebene_1" xmlns="http://www.w3.org/2000/svg"  x="0px" y="0px"' +
                     'width="61.3" height="84.6" viewBox="0 0 61.3 84.6" enable-background="new 0 0 61.3 84.6" xml:space="preserve">' +
                '<style type="text/css">' +
                    '.st0{display:none;}' +
                    '.st1{display:inline;fill:#969696;}' +
                    '.st2{display:inline;fill:white;}' +
                    '.st3{fill:#969696;}' +
                    '.st4{fill:white;}' +
                '</style>' +
                '<g>' +
                    '<g class="st0">' +
                        '<path class="st1" d="M7.4,113.2c1.6,0,2.9-0.6,3.8-1.9l2,2.1c-1.6,1.8-3.5,2.7-5.7,2.7c-2.2,0-4-0.7-5.4-2.1' +
                            'C0.7,112.7,0,111,0,108.8c0-2.1,0.7-3.9,2.2-5.3c1.5-1.4,3.2-2.1,5.3-2.1c2.3,0,4.3,0.9,5.9,2.7l-2,2.3c-1-1.3-2.3-1.9-3.8-1.9' +
                            'c-1.2,0-2.2,0.4-3.1,1.2c-0.9,0.8-1.3,1.8-1.3,3.2c0,1.3,0.4,2.4,1.2,3.2C5.3,112.8,6.3,113.2,7.4,113.2z"/>' +
                        '<path class="st1" d="M26.1,110.5c0,1.6-0.6,2.9-1.7,4c-1.1,1.1-2.5,1.6-4.2,1.6s-3.1-0.5-4.2-1.6c-1.1-1.1-1.7-2.4-1.7-4' +
                            'c0-1.6,0.6-2.9,1.7-4c1.1-1.1,2.5-1.6,4.2-1.6s3.1,0.5,4.2,1.6C25.6,107.6,26.1,108.9,26.1,110.5z M17.6,110.5' +
                            'c0,0.9,0.3,1.6,0.8,2.2c0.5,0.6,1.2,0.8,2,0.8s1.5-0.3,2-0.8c0.5-0.6,0.8-1.3,0.8-2.2c0-0.9-0.3-1.6-0.8-2.2' +
                            'c-0.5-0.6-1.2-0.9-2-0.9s-1.5,0.3-2,0.9C17.8,108.9,17.6,109.6,17.6,110.5z"/>' +
                        '<path class="st1" d="M34.2,107.7c-0.9,0-1.6,0.3-2,1c-0.5,0.6-0.7,1.5-0.7,2.6v4.8h-3.1v-11h3.1v1.5c0.4-0.5,0.109-0.8,1.5-1.1' +
                            'c0.6-0.3,1.2-0.5,1.8-0.5l0,2.9H34.2z"/>' +
                        '<path class="st1" d="M46.3,114.4c-1.2,1.2-2.7,1.8-4.4,1.8s-3.1-0.5-4.1-1.5c-1.1-1-1.6-2.4-1.6-4.1c0-1.7,0.6-3.1,1.7-4.1' +
                            'c1.1-1,2.4-1.5,3.9-1.5c1.5,0,2.8,0.5,3.9,1.4c1.1,0.9,1.6,2.2,1.6,3.8v1.6h-8c0.1,0.6,0.4,1.1,0.9,1.5c0.5,0.4,1.1,0.6,1.8,0.6' +
                            'c1.1,0,2-0.4,2.7-1.1L46.3,114.4z M43.4,107.9c-0.4-0.4-0.9-0.5-1.5-0.5c-0.6,0-1.2,0.2-1.7,0.6c-0.5,0.4-0.8,0.9-0.9,1.5h4.8' +
                            'C44,108.8,43.8,108.3,43.4,107.9z"/>' +
                        '<path class="st2" d="M52.9,104.6c-0.3,0.3-0.5,0.6-0.5,1s0.2,0.7,0.6,1c0.4,0.2,1.2,0.5,2.6,0.9c1.4,0.3,2.4,0.8,3.2,1.5' +
                            'c0.8,0.7,1.1,1.6,1.1,2.9c0,1.3-0.5,2.3-1.4,3.1c-1,0.8-2.2,1.2-3.8,1.2c-2.3,0-4.3-0.8-6.1-2.5l1.9-2.3c1.5,1.3,3,2,4.3,2' +
                            'c0.6,0,1-0.1,1.4-0.4c0.3-0.3,0.5-0.6,0.5-1c0-0.4-0.2-0.8-0.5-1c-0.4-0.3-1.1-0.5-2.1-0.8c-1.7-0.4-2.9-0.9-3.7-1.5' +
                            'c-0.8-0.6-1.2-1.6-1.2-3c0-1.4,0.5-2.4,1.5-3.1c1-0.7,2.2-1.1,3.7-1.1c1,0,1.9,0.2,2.9,0.5c1,0.3,1.8,0.8,2.5,1.4l-1.6,2.3' +
                            'c-1.2-0.9-2.5-1.4-3.8-1.4C53.6,104.2,53.2,104.3,52.9,104.6z"/>' +
                        '<path class="st2" d="M65.4,110.1v5.9h-3.1v-15.2h3.1v5.4c0.9-0.9,2-1.4,3.1-1.4s2.1,0.4,2.9,1.2c0.8,0.8,1.2,1.9,1.2,3.3v6.7h-3.1' +
                            'v-6c0-1.7-0.6-2.5-1.9-2.5c-0.6,0-1.1,0.2-1.6,0.7C65.6,108.6,65.4,109.2,65.4,110.1z"/>' +
                        '<path class="st2" d="M86.4,110.5c0,1.6-0.6,2.9-1.7,4c-1.1,1.1-2.5,1.6-4.2,1.6c-1.7,0-3.1-0.5-4.2-1.6c-1.1-1.1-1.7-2.4-1.7-4' +
                            'c0-1.6,0.6-2.9,1.7-4c1.1-1.1,2.5-1.6,4.2-1.6c1.7,0,3.1,0.5,4.2,1.6C85.8,107.6,86.4,108.9,86.4,110.5z M77.8,110.5' +
                            'c0,0.9,0.3,1.6,0.8,2.2c0.5,0.6,1.2,0.8,2,0.8s1.5-0.3,2-0.8c0.5-0.6,0.8-1.3,0.8-2.2c0-0.9-0.3-1.6-0.8-2.2' +
                            'c-0.5-0.6-1.2-0.9-2-0.9s-1.5,0.3-2,0.9C78.1,108.9,77.8,109.6,77.8,110.5z"/>' +
                        '<path class="st2" d="M95.1,104.8c1.3,0,2.4,0.5,3.4,1.6c1,1.1,1.5,2.4,1.5,4c0,1.6-0.5,3-1.5,4.1c-1,1.1-2.2,1.6-3.5,1.6' +
                            'c-1.3,0-2.4-0.5-3.2-1.6v5.4h-3.1v-15h3.1v1.2C92.7,105.3,93.8,104.8,95.1,104.8z M91.7,110.5c0,0.9,0.2,1.6,0.7,2.2' +
                            'c0.5,0.6,1.1,0.8,1.8,0.8c0.7,0,1.3-0.3,1.9-0.8c0.5-0.6,0.8-1.3,0.8-2.2c0-0.9-0.3-1.6-0.8-2.2c-0.5-0.6-1.1-0.9-1.9-0.9' +
                            's-1.3,0.3-1.8,0.9C91.9,108.9,91.7,109.6,91.7,110.5z"/>' +
                    '</g>' +
                    '<g>' +
                        '<path id="Shake" class="st3" d="M60.8,33.7c0-0.1-8.8-20.3-8.8-20.3c-0.7-1.3-1.4-2-2.7-2.5c-1.3-0.6-2.2-0.7-3.7-0.4l-2.9,0.9' +
                            'c0,1.7,0.2,3.4,0.6,4.7c0,0.1,0.1,0.2,0.1,0.3c0.8-1.3,2.5-1.9,4-1.2c1.6,0.7,2.3,2.6,1.6,4.2c-0.7,1.6-2.6,2.3-4.2,1.5' +
                            'c-1-0.5-1.7-1.4-1.8-2.4c0,0,0,0,0,0c-1.3-2.2-1.4-5.6-1.4-6.7l-16.7,5.2c-2.3,1.1-4.1,2-5.2,4.3c-0.8,1.8-4.2,9.1-7.9,17.2' +
                            'L3.3,56.8C1.5,60.5,0.4,63,0.4,63c-1,2.3,0,5,2.2,6c0,0,29.8,13.7,33,15.2c2.3,1,5,0,6-2.2l9.9-21.6l0.8-1.7l8.5-18.5' +
                            'c0.8-1.8,0.7-3.4,0.3-5.1"/>' +
                        '<g>' +
                            '<path class="st4" d="M47.5,0c-1.8-0.1-2.9,1.8-3.4,3.3c-0.8,2-1.2,4.1-1.3,6.3c-0.2,2.4,0,4.7,0.5,6.5c0.4,1.3,1.1,3.1,2.6,3.4' +
                                'c0.7,0.2,1.5-0.1,2-0.5c0.5-0.4,0.9-1,1.1-1.3c0.1-0.1,0.1-0.2,0.1-0.2c-0.1-0.9-0.7-1.7-1.5-2.2c-0.2,0.4-0.3,0.8-0.5,1.1' +
                                'c-0.1,0.2-0.5,1-0.9,0.8c-0.5-0.2-0.8-1.7-1-2.3c-0.3-1.3-0.5-3-0.3-5.2c0.3-4.4,1.6-7.1,2.3-7.4c0.5,0.2,1.6,2.5,1.4,6.9' +
                                'c0,0,1.1,0.3,2.2,1c0-0.1,0.2-2-0.1-4.2c-0.2-1.8-0.5-3.9-1.9-5.3C48.6,0.3,48.1,0,47.5,0z"/>' +
                        '</g>' +
                    '</g>' +
                '</g>' +
                '</svg>' +
                '</li>');
            Ext.get('pimcore_menu_coreshop').on('mousedown', function (e, el) {
                toolbar.showSubMenu.call(this._menu, e, el);
            }.bind(this));

            coreshop.broker.fireEvent('coreShop.menu.initialized', this, this._menu);
        }

        Ext.get('coreshop_status').set(
            {
                'data-menu-tooltip': t('coreshop_loaded').format(coreshop.settings.bundle.version),
                class: ''
            }
        );

        pimcore.helpers.initMenuTooltips();

        //Add Report Definition
        pimcore.report.broker.addGroup('coreshop', 'coreshop_reports', 'coreshop_icon_report');
        //pimcore.report.broker.addGroup('coreshop_monitoring', 'coreshop_monitoring', 'coreshop_icon_monitoring');

        Ext.each(coreshop.settings.reports, function(report) {
            if (coreshop.report.reports.hasOwnProperty(report)) {
                report = coreshop.report.reports[report];

                pimcore.report.broker.addReport(report, 'coreshop', {
                    name: report.prototype.getName(),
                    text: report.prototype.getName(),
                    niceName: report.prototype.getName(),
                    iconCls: report.prototype.getIconCls()
                });
            }
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
         if (tab.data.general.o_className === coreshop.class_map.coreshop.order) {
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
        } else if (tab.data.general.o_className === coreshop.class_map.coreshop.order_invoice) {
            var resetChangesFunction = tab.resetChanges;

            var renderTab = new coreshop.invoice.render(tab);

            tab.tabbar.add(renderTab.getLayout());

            tab.resetChanges = function () {
                resetChangesFunction.call(tab);

                renderTab.reload();
            };
        } else if (tab.data.general.o_className === coreshop.class_map.coreshop.order_shipment) {
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

    openCreateOrder: function () {
        coreshop.global.resource.open('coreshop.order', 'create_order');
    },

    openQuotes: function () {
        coreshop.global.resource.open('coreshop.order', 'quotes');
    },

    openCreateQuote: function () {
        coreshop.global.resource.open('coreshop.order', 'create_quote');
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

coreshop.broker.addListener('pimcore.ready', function() {
    new coreshop.core.resource();
});
