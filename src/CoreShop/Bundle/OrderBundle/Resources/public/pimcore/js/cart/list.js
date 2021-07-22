/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.cart.list');
coreshop.order.cart.list = Class.create(coreshop.order.sale.list, {
    type: 'cart',

    enhanceGridLayout: function($super, grid) {
        $super(grid);

        grid.getStore().getProxy().setExtraParam('coreshop_cart', 1);
        grid.getStore().getProxy().abort();

        grid.getStore().load();
    },

    setupContextMenuPlugin: function () {
        this.contextMenuPlugin = new coreshop.pimcore.plugin.grid(
            'coreshop_cart',
            function (id) {
                this.open(id);
            }.bind(this),
            [coreshop.class_map.coreshop.cart],
            this.getGridPaginator()
        );
    },

    open: function (id, callback) {
        coreshop.order.helper.openCart(id, callback);
    }
});
