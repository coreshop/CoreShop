/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.order.list');
coreshop.order.order.list = Class.create(coreshop.order.sale.list, {
    type: 'order',

    open: function (record, callback) {
        coreshop.order.helper.openOrder(record.get('o_id'), callback);
    },

    orderStateRenderer: function (orderStateInfo) {
        var bgColor = orderStateInfo.color,
            textColor = coreshop.helpers.constrastColor(bgColor);
        return '<span class="rounded-color" style="background-color:' + bgColor + '; color: ' + textColor + '">' + orderStateInfo.label + '</span>';
    },

    orderShippingStateRenderer: function (orderStateInfo) {
        var bgColor = coreshop.helpers.hexToRgb(orderStateInfo.color),
            textColor = 'black';
        return '<span class="rounded-color" style="background-color: rgba(' + bgColor.join(',') + ', 0.2); color: ' + textColor + '">' + orderStateInfo.label + '</span>';
    },

    orderPaymentStateRenderer: function (orderStateInfo) {
        var bgColor = coreshop.helpers.hexToRgb(orderStateInfo.color),
            textColor = 'black';
        return '<span class="rounded-color" style="background-color: rgba(' + bgColor.join(',') + ', 0.2); color: ' + textColor + '">' + orderStateInfo.label + '</span>';
    },

    orderInvoiceStateRenderer: function (orderStateInfo) {
        var bgColor = coreshop.helpers.hexToRgb(orderStateInfo.color),
            textColor = 'black';
        return '<span class="rounded-color" style="background-color: rgba(' + bgColor.join(',') + ', 0.2); color: ' + textColor + '">' + orderStateInfo.label + '</span>';
    }
});
