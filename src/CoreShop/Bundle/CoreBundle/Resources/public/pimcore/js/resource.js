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

        coreshop.broker.fireEvent('resource.register', 'coreshop.core', this);
    },

    openResource: function (item) {
        if (item === 'about') {
            coreshop.helpers.showAbout();
        } else if (item === 'settings') {
            this.openSettings();
        }
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
        new coreshop.menu.coreshop.main();

        Ext.get('coreshop_status').set(
            {
                'data-menu-tooltip': t('coreshop_loaded').format(coreshop.settings.bundle.version),
                class: ''
            }
        );

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
    }
});

coreshop.broker.addListener('pimcore.ready', function() {
    new coreshop.core.resource();
});
