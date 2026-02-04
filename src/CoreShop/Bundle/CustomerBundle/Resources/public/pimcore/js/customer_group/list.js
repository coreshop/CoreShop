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

pimcore.registerNS('coreshop.customer_group.list');
coreshop.customer_group.list = Class.create(coreshop.resource.list, {
    type: 'customer_group',

    url: {
        folder: 'coreshop_customer_group_folderConfiguration'
    },

    setupContextMenuPlugin: function () {
        this.contextMenuPlugin = new coreshop.pimcore.plugin.grid(
            'coreshop_customer_group',
            function (id) {
                this.open(id);
            }.bind(this),
            [coreshop.class_map.coreshop.customer_group],
            this.getGridPaginator()
        );
    },

    open: function (id, callback) {
        pimcore.helpers.openObject(id, 'object');
        //coreshop.order.helper.openOrder(id, callback);
    }
});
