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
