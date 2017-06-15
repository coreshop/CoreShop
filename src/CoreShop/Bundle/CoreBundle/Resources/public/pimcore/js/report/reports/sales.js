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

pimcore.registerNS('coreshop.report.reports.sales');
coreshop.report.reports.sales = Class.create(coreshop.report.abstract, {

    url: '/admin/coreshop/reports/get-sales-report',

    getName: function () {
        return t('coreshop_report_sales');
    },

    getIconCls: function () {
        return 'coreshop_icon_report_sales';
    },

    getGroupByField: function () {
        return this.panel.down('[name=groupBy]');
    },

    getFilterParams: function ($super) {
        var fields = $super();
        fields.groupBy = this.getGroupByField().getValue();
        return fields;
    },

    getDocketItemsForPanel: function ($super) {

        var fields = $super();

        fields.push(
            {
                xtype: 'toolbar',
                dock: 'top',
                items: this.getAdditionalFilterFields()
            }
        );

        return fields;

    },

    getAdditionalFilterFields: function () {

        var fields = [];

        fields.push(
            {
                xtype: 'combo',
                fieldLabel: t('coreshop_report_groups'),
                name: 'groupBy',
                value: 'day',
                width: 250,
                store: [['day', t('coreshop_report_groups_day')], ['month', t('coreshop_report_groups_month')], ['year', t('coreshop_report_groups_year')]],
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                queryMode: 'local'
            }
        );

        return fields;
    },

    getGrid: function () {
        return new Ext.Panel({
            layout: 'fit',
            height: 275,
            items: {
                xtype: 'cartesian',
                store: this.getStore(),
                legend: {
                    docked: 'right'
                },
                interactions: ['itemhighlight',
                    {
                        type: 'panzoom',
                        zoomOnPanGesture: true
                    }
                ],
                axes: [{
                    type: 'numeric',
                    fields: ['sales'],
                    position: 'left',
                    grid: true,
                    minimum: 0
                }, {
                    type: 'category',
                    fields: 'datetext',
                    position: 'bottom'
                }
                ],
                series: [
                    {
                        type: 'line',
                        axis: ' left',
                        title: t('coreshop_sales'),
                        xField: 'datetext',
                        yField: 'sales',
                        colors: ['#01841c'],
                        style: {
                            lineWidth: 2,
                            stroke: '#01841c'
                        },
                        marker: {
                            radius: 4,
                            fillStyle: '#01841c'
                        },
                        highlight: {
                            fillStyle: '#000',
                            radius: 5,
                            lineWidth: 2,
                            strokeStyle: '#fff'
                        },
                        tooltip: {
                            trackMouse: true,
                            style: 'background: #01841c',
                            renderer: function (tooltip, storeItem, item) {
                                var title = item.series.getTitle();
                                tooltip.setHtml(title + ' for ' + storeItem.get('datetext') + ': ' + storeItem.get('salesFormatted'));
                            }
                        }
                    }
                ]
            }
        });
    }
});
