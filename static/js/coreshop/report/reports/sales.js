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

pimcore.registerNS("pimcore.plugin.coreshop.report.reports.sales");
pimcore.plugin.coreshop.report.reports.sales = Class.create(pimcore.plugin.coreshop.report.abstract, {
    getName: function () {
        return t("coreshop_report_sales");
    },

    getIconCls: function () {
        return "coreshop_icon_report_sales";
    },

    getGrid : function() {
        var store = new Ext.data.Store({
            autoDestroy: true,
            proxy: {
                type: 'ajax',
                url: '/plugin/CoreShop/admin_statistics/get-sales-from-last-days',
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }},
            fields: ['timestamp','datetext',"sales"]
        });

        store.load();


        var panel = new Ext.Panel({
            layout:'fit',
            height: 275,
            items: {
                xtype: 'cartesian',
                store: store,
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
                }
                    , {
                        type: 'category',
                        fields: 'datetext',
                        position: 'bottom'
                    }
                ],
                series: [
                    {
                        type: 'line',
                        axis:' left',
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
                            renderer: function(tooltip, storeItem, item) {
                                var title = item.series.getTitle();
                                tooltip.setHtml(title + ' for ' + storeItem.get('datetext') + ': ' + storeItem.get("salesFormatted"));
                            }
                        }
                    }
                ]
            }
        });

        return panel;
    }
});
