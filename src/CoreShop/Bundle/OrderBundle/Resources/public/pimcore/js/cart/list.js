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

pimcore.registerNS('coreshop.order.cart.list');
coreshop.order.cart.list = Class.create(coreshop.order.order.list, {
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
            [coreshop.class_map.coreshop.order],
            this.getGridPaginator()
        );
    },

    open: function (id, callback) {
        coreshop.order.helper.openCart(id, callback);
    }
});
