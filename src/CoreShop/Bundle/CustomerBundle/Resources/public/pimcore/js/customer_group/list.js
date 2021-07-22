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

pimcore.registerNS('coreshop.customer_group.list');
coreshop.customer_group.list = Class.create(coreshop.resource.list, {
    type: 'customer_group',

    url: {
        folder: '/admin/coreshop/customer_groups/folder-configuration'
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
