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
