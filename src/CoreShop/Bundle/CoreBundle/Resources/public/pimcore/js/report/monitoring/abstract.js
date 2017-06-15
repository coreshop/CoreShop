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

pimcore.registerNS('coreshop.report.monitoring.abstract');
coreshop.report.monitoring.abstract = Class.create(coreshop.report.abstract, {

    url: '',

    getName: function () {
        return 'coreshop_monitoring';
    },

    getIconCls: function () {
        return 'coreshop_icon_monitoring';
    },

    getGrid: function () {
        return false;
    },

    getPanel: function () {

        if (!this.panel) {
            this.panel = new Ext.Panel({
                title: this.getName(),
                layout: 'fit',
                border: false,
                items: [],
                dockedItems: {
                    xtype: 'toolbar',
                    dock: 'top',
                    items: this.getFilterFields()
                }
            });

            grid = this.getGrid();

            if (grid) {
                this.panel.add(grid);
            }

            this.filter();
        }

        return this.panel;
    },

    getFilterFields: function () {
        return [];
    },

    getStore: function () {
        if (!this.store) {
            this.store = new Ext.data.Store({
                autoDestroy: true,
                proxy: {
                    type: 'ajax',
                    url: this.url,
                    actionMethods: {
                        read: 'POST'
                    },
                    reader: {
                        type: 'json',
                        rootProperty: 'data'
                    }
                },
                fields: ['timestamp', 'text', 'data']
            });
        }

        return this.store;
    },

    filter: function () {
        this.getStore().load({
            params: this.getFilterParams()
        });
    },

    getFilterParams: function () {
        return {};
    }
});
