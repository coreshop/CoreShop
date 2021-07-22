/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
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
        } else if (item === 'customer_to_company_assign_to_new') {
            this.openAssignCustomerToNewCompany();
        } else if (item === 'customer_to_company_assign_to_existing') {
            this.openAssignCustomerToExistingCompany();
        }
    },

    pimcoreReady: function (params, broker) {
        Ext.Ajax.request({
            url: '/admin/coreshop/settings/get-settings',
            success: function (response) {
                this.settings = Ext.decode(response.responseText);
                coreshop.settings = this.settings;
                this.initializeCoreShop();
            }.bind(this)
        });
    },

    initializeCoreShop: function () {
        new coreshop.menu.coreshop.main();

        //Add Report Definition
        pimcore.report.broker.addGroup('coreshop', 'coreshop_reports', 'coreshop_icon_report');

        Ext.each(coreshop.settings.reports, function (report) {
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

    postOpenObject: function (tab) {

        switch (tab.data.general.o_className) {
            case coreshop.class_map.coreshop.order:
                this._enrichOrderObject(tab);
                break;

            case coreshop.class_map.coreshop.order_invoice:
                this._enrichInvoiceObject(tab);
                break;

            case coreshop.class_map.coreshop.order_shipment:
                this._enrichShipmentObject(tab);
                break;

            case coreshop.class_map.coreshop.product:
                this._enrichProductObject(tab);
                break;

            case coreshop.class_map.coreshop.category:
                this._enrichCategoryObject(tab);
                break;
        }

        pimcore.layout.refresh();
    },

    openSettings: function () {
        try {
            pimcore.globalmanager.get('coreshop_settings').activate();
        } catch (e) {
            pimcore.globalmanager.add('coreshop_settings', new coreshop.core.settings());
        }
    },

    openAssignCustomerToNewCompany: function () {
        try {
            pimcore.globalmanager.get('coreshop_customer_to_new_company_assignment').activate();
        } catch (e) {
            pimcore.globalmanager.add('coreshop_customer_to_new_company_assignment', new coreshop.core.customer.customerToCompanyTransformer());
        }
    },

    openAssignCustomerToExistingCompany: function () {
        try {
            pimcore.globalmanager.get('coreshop_customer_to_existing_company_assignment').activate();
        } catch (e) {
            pimcore.globalmanager.add('coreshop_customer_to_existing_company_assignment', new coreshop.core.customer.customerToCompanyAssigner());
        }
    },

    _enrichOrderObject: function (tab) {
        var orderMoreButtons = [];

        orderMoreButtons.push({
            text: t('coreshop_add_payment'),
            scale: 'medium',
            iconCls: 'coreshop_icon_currency',
            handler: function () {
                coreshop.order.order.createPayment.showWindow(tab.id, tab.data.data, function () {
                    tab.reload(tab.data.currentLayoutId);
                });
            }.bind(this, tab)
        });

        orderMoreButtons.push({
            text: t('open'),
            scale: 'medium',
            iconCls: 'coreshop_icon_order',
            handler: function () {
                coreshop.order.helper.openOrder(tab.id);
            }.bind(this, tab)
        });

        tab.toolbar.insert(tab.toolbar.items.length, '-');

        tab.toolbar.insert(tab.toolbar.items.length, {
            text: t('coreshop_more'),
            scale: 'medium',
            iconCls: 'coreshop_icon_logo',
            menu: orderMoreButtons
        });

    },

    _enrichInvoiceObject: function (tab) {
        var resetChangesFunction = tab.resetChanges,
            renderTab = new coreshop.invoice.render(tab);

        tab.tabbar.add(renderTab.getLayout());

        tab.resetChanges = function () {
            resetChangesFunction.call(tab);

            renderTab.reload();
        };
    },

    _enrichShipmentObject: function (tab) {
        var resetChangesFunction = tab.resetChanges,
            renderTab = new coreshop.shipment.render(tab);

        tab.tabbar.add(renderTab.getLayout());

        tab.resetChanges = function () {
            resetChangesFunction.call(tab);

            renderTab.reload();
        };
    },

    _enrichCategoryObject: function (tab) {
        tab.tabbar.insert(1, new coreshop.core.object.store_preview(tab).getLayout());
    },

    _enrichProductObject: function (tab) {
        var productMoreButtons = [];

        tab.tabbar.insert(1, new coreshop.core.object.store_preview(tab).getLayout());

        if (tab.data.general.o_type === 'object') {
            productMoreButtons.push({
                text: t('coreshop_solidify_variant_unit_definition_data'),
                scale: 'medium',
                iconCls: 'coreshop_icon_product_unit',
                handler: function () {
                    new coreshop.product.workflow.variantUnitDefinitionSolidifier(tab.data, tab.tab);
                }.bind(this, tab)
            });
        }

        if (productMoreButtons.length === 0) {
            return;
        }

        tab.toolbar.insert(tab.toolbar.items.length, '-');

        tab.toolbar.insert(tab.toolbar.items.length, {
            text: t('coreshop_more'),
            scale: 'medium',
            iconCls: 'coreshop_icon_logo',
            menu: productMoreButtons
        });
    }
});

coreshop.broker.addListener('pimcore.ready', function () {
    new coreshop.core.resource();
});
