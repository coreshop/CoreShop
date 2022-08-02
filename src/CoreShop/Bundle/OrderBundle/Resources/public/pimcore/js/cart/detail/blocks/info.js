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

pimcore.registerNS('coreshop.order.cart.detail.blocks.info');
coreshop.order.cart.detail.blocks.info = Class.create(coreshop.order.order.detail.blocks.info, {
    updateSale: function () {
        var me = this;

        me.saleInfo.setTitle(t('coreshop_' + me.panel.type) + ': ' + this.sale.o_id);
    }
});
