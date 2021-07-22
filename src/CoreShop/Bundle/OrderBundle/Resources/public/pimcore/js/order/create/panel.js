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

pimcore.registerNS('coreshop.order.order.create');
pimcore.registerNS('coreshop.order.order.create.panel');
coreshop.order.order.create.panel = Class.create(coreshop.order.sale.create.panel, {
    type: 'order',

    prepareSuccessMessage: function(message, response) {
        if (response.hasOwnProperty('reviseLink') && response.reviseLink) {
            message += '<div class="coreshop-order-create-revise">';
            message += '<span class="coreshop-order-create-revise-desc">' + t('coreshop_creating_order_finished_revise_link') + '</span>';
            message += '<span class="coreshop-order-create-revise-link">' + response.reviseLink + '</span>';
            message += '</div>';
        }

        return message;
    },
});
