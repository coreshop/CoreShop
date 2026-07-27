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

pimcore.registerNS('coreshop.order.quote.list');
coreshop.order.quote.list = Class.create(coreshop.order.order.list, {
    type: 'quote',

    open: function (id, callback) {
        coreshop.order.helper.openQuote(id, callback);
    },

    setupContextMenuPlugin: function () {
        this.contextMenuPlugin = new coreshop.pimcore.plugin.grid(
            'coreshop_quote',
            function (id) {
                this.open(id);
            }.bind(this),
            [coreshop.class_map.coreshop.order],
            this.getGridPaginator()
        );
    }
});

