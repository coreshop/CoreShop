/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.report.reports.payment_providers');
coreshop.report.reports.payment_providers = Class.create(coreshop.report.abstractStore, {

    reportType: 'payment_providers',

    getName: function () {
        return t('coreshop_report_payments');
    },

    getIconCls: function () {
        return 'coreshop_icon_report_payments';
    },

    getGrid: function () {
        return new Ext.Panel({
            layout: 'fit',
            height: 275,
            items: {
                xtype: 'polar',
                reference: 'chart',
                theme: 'default-gradients',
                width: '100%',
                height: 500,
                insetPadding: 50,
                innerPadding: 20,
                store: this.getStore(),
                legend: {
                    docked: 'bottom'
                },
                interactions: ['rotate'],
                series: [{
                    type: 'pie',
                    angleField: 'data',
                    label: {
                        field: 'provider',
                        calloutLine: {
                            length: 60,
                            width: 3

                            // specifying 'color' is also possible here
                        }
                    },
                    highlight: true,
                    tooltip: {
                        trackMouse: true,
                        renderer: function (tooltip, record, item) {
                            tooltip.setHtml(record.get('provider') + ': ' + record.get('data') + '%');
                        }
                    }
                }]
            }
        });
    }
});
