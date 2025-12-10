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

pimcore.registerNS('coreshop.order.quote.detail.panel');
coreshop.order.quote.detail.panel = Class.create(coreshop.order.order.detail.panel, {
    type: 'quote',

    getBlockIdentifier: function () {
        return coreshop.order.quote.detail.blocks;
    },

    getLayout: function($super) {
        var layout = $super();

        layout.setTitle(t('coreshop_' + this.type) + ': ' + this.sale.quoteNumber);
    },
});
