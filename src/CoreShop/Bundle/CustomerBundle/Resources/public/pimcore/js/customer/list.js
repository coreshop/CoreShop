/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.customer.list');
coreshop.customer.list = Class.create(coreshop.resource.list, {
    type: 'customer',

    url: {
        folder: 'coreshop_customer_folderConfiguration'
    },

    setupContextMenuPlugin: function () {
        this.contextMenuPlugin = new coreshop.pimcore.plugin.grid(
            'coreshop_customer',
            function (id) {
                this.open(id);
            }.bind(this),
            [coreshop.class_map.coreshop.customer],
            this.getGridPaginator()
        );
    },

    open: function (id, callback) {
        pimcore.helpers.openObject(id, 'object');
        //coreshop.order.helper.openOrder(id, callback);
    }
});
