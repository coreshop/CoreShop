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

pimcore.registerNS("pimcore.plugin.coreshop.report.abstract");
pimcore.plugin.coreshop.report.abstract = Class.create(pimcore.report.abstract, {

    drillDownFilters: {},
    drillDownStores: [],

    matchType: function (type) {
        var types = ["global"];
        if (pimcore.report.abstract.prototype.matchTypeValidate(type, types)) {
            return true;
        }
        return false;
    },

    getName: function () {
        return "coreshop";
    },

    getIconCls: function () {
        return "coreshop_icon_report";
    },

    getGrid : function() {
        return false;
    },

    getPanel: function () {

        if(!this.panel) {
            this.panel = new Ext.Panel({
                title: this.getName(),
                layout: "fit",
                border: false,
                items: []
            });

            grid = this.getGrid();

            if(grid) {
                this.panel.add(grid);
            }
        }

        return this.panel;
    }
});

