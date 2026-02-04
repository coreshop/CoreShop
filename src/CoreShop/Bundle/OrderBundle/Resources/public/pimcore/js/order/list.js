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

pimcore.registerNS('coreshop.order.order.list');
coreshop.order.order.list = Class.create(coreshop.resource.list, {
    supportsCreate: true,
    type: 'order',

    generateUrl: function() {
        return Routing.generate('coreshop_admin_order_get_folder_configuration', {'saleType': this.type});
    },

    setupContextMenuPlugin: function () {
        this.contextMenuPlugin = new coreshop.pimcore.plugin.grid(
            'coreshop_order',
            function (id) {
                this.open(id);
            }.bind(this),
            [coreshop.class_map.coreshop.order],
            this.getGridPaginator()
        );
    },

    open: function (id, callback) {
        coreshop.order.helper.openOrder(id, callback);
    }
});
