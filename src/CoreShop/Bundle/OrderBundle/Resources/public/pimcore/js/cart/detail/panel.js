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

pimcore.registerNS('coreshop.order.cart.detail.panel');
coreshop.order.cart.detail.panel = Class.create(coreshop.order.order.detail.panel, {
    type: 'cart',

    getBlockIdentifier: function () {
        return coreshop.order.cart.detail.blocks;
    },

    getLayout: function($super) {
        var layout = $super();

        layout.setTitle(t('coreshop_' + this.type) + ': ' + this.sale.id);
    },
});
