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

pimcore.registerNS('coreshop.report.reports.carriers');
coreshop.report.reports.carriers = Class.create(coreshop.report.abstractStore, {

    reportType: 'carriers',

    getName: function () {
        return t('coreshop_report_carriers');
    },

    getIconCls: function () {
        return 'coreshop_icon_report_carriers';
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
                        field: 'carrier',
                        calloutLine: {
                            length: 60,
                            width: 3
                        }
                    },
                    highlight: true,
                    tooltip: {
                        trackMouse: true,
                        renderer: function (tooltip, record, item) {
                            tooltip.setHtml(record.get('carrier') + ': ' + record.get('data') + '%');
                        }
                    }
                }]
            }
        });
    }
});
