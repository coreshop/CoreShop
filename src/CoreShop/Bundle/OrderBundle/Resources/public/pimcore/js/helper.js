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

pimcore.registerNS('coreshop.order.helper');
pimcore.registerNS('coreshop.order.helper.x');

coreshop.order.helper.openSale = function (id, type, callback) {
    if (type === "order") {
        coreshop.order.helper.openOrder(id, callback);
    }
    else if(type === "quote") {
        coreshop.order.helper.openQuote(id, callback);
    }
};

coreshop.order.helper.openOrder = function (id, callback) {
    if (pimcore.globalmanager.exists('coreshop_order_' + id) === false) {

        pimcore.globalmanager.add('coreshop_order_' + id, true);

        Ext.Ajax.request({
            url: '/admin/coreshop/order/detail',
            params: {
                id: id
            },
            success: function (response) {
                var res = Ext.decode(response.responseText);

                if (res.success) {
                    pimcore.globalmanager.add('coreshop_order_' + id, new coreshop.order.order.detail(res.sale));
                } else {
                    //TODO: Show messagebox
                    Ext.Msg.alert(t('open_target'), t('problem_opening_new_target'));
                }

                if (Ext.isFunction(callback)) {
                    callback();
                }
            }.bind(this)
        });
    } else {
        var tab = pimcore.globalmanager.get('coreshop_order_' + id);

        if (Ext.isObject(tab) && Ext.isFunction(tab.activate)) {
            tab.activate();
        }

        if (Ext.isFunction(callback)) {
            callback();
        }
    }
};

coreshop.order.helper.openQuote = function (id, callback) {
    if (pimcore.globalmanager.exists('coreshop_quote_' + id) === false) {

        pimcore.globalmanager.add('coreshop_quote_' + id, true);

        Ext.Ajax.request({
            url: '/admin/coreshop/quote/detail',
            params: {
                id: id
            },
            success: function (response) {
                var res = Ext.decode(response.responseText);

                if (res.success) {
                    pimcore.globalmanager.add('coreshop_quote_' + id, new coreshop.order.quote.detail(res.sale));
                } else {
                    //TODO: Show messagebox
                    Ext.Msg.alert(t('open_target'), t('problem_opening_new_target'));
                }

                if (Ext.isFunction(callback)) {
                    callback();
                }
            }.bind(this)
        });
    } else {
        var tab = pimcore.globalmanager.get('coreshop_quote_' + id);

        if (Ext.isObject(tab) && Ext.isFunction(tab.activate)) {
            tab.activate();
        }

        if (Ext.isFunction(callback)) {
            callback();
        }
    }
};
