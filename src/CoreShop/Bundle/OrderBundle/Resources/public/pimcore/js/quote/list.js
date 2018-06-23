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

pimcore.registerNS('coreshop.order.quote.list');
coreshop.order.quote.list = Class.create(coreshop.order.sale.list, {
    type: 'quote',

    setupContextMenuPlugin: function () {
        this.contextMenuPlugin = new coreshop.sales.plugin.salesListContextMenu(
            function (id) {
                this.open(id);
            }.bind(this),
            [coreshop.class_map.coreshop.quote],
            this.getBulkStore(),
            this.getGridPaginator()
        );
    },

    open: function (id, callback) {
        coreshop.order.helper.openQuote(id, callback);
    }
});

