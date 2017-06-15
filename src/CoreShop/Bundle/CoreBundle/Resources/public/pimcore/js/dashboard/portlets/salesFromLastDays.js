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

pimcore.registerNS('pimcore.layout.portlets.salesFromLastDays');
pimcore.layout.portlets.salesFromLastDays = Class.create(pimcore.layout.portlets.abstract, {

    getType: function () {
        return 'pimcore.layout.portlets.salesFromLastDays';
    },

    getName: function () {
        return t('coreshop_sales_from_last_days');
    },

    getIcon: function () {
        return 'coreshop_carrier_costs_icon';
    },

    getLayout: function (portletId) {

        var store = new Ext.data.Store({
            autoDestroy: true,
            proxy: {
                type: 'ajax',
                url: '/admin/coreshop/reports/get-sales-report',
                extraParams: {
                    'filters[from]': new Date(new Date().getFullYear(), new Date().getMonth(), 1).getTime() / 1000,
                    'filters[to]': new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).getTime() / 1000
                },
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }
            },
            fields: ['timestamp', 'datetext', 'sales']
        });

        store.load();

        var panel = new Ext.Panel({
            layout: 'fit',
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

        this.layout = Ext.create('Portal.view.Portlet', Object.extend(this.getDefaultConfig(), {
            title: this.getName(),
            iconCls: this.getIcon(),
            height: 275,
            layout: 'fit',
            items: [panel]
        }));

        this.layout.portletId = portletId;
        return this.layout;
    }
});
