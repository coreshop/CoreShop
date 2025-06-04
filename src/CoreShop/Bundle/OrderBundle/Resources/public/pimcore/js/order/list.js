/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
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
