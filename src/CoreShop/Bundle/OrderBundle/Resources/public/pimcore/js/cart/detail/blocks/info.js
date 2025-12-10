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

pimcore.registerNS('coreshop.order.cart.detail.blocks.info');
coreshop.order.cart.detail.blocks.info = Class.create(coreshop.order.order.detail.blocks.info, {
    updateSale: function () {
        var me = this;

        me.saleInfo.setTitle(t('coreshop_' + me.panel.type) + ': ' + this.sale.id);
    }
});
