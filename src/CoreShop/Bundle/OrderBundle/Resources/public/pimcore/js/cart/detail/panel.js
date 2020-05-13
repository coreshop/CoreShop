/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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

        layout.setTitle(t('coreshop_' + this.type) + ': ' + this.sale.o_id);
    },
});
