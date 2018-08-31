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
    var cacheIdentifier = 'coreshop_'+type+'_' + id;

    if (pimcore.globalmanager.exists(cacheIdentifier) === false) {
        pimcore.globalmanager.add(cacheIdentifier, true);

        Ext.Ajax.request({
            url: '/admin/coreshop/'+type+'/detail',
            params: {
                id: id
            },
            success: function (response) {
                var res = Ext.decode(response.responseText);

                if (res.success) {
                    pimcore.globalmanager.add(cacheIdentifier, new coreshop.order[type].detail.panel(res.sale));
                } else {
                    Ext.Msg.alert(t('open_target'), t('problem_opening_new_target'));
                }

                if (Ext.isFunction(callback)) {
                    callback();
                }
            }.bind(this)
        });
    } else {
        var tab = pimcore.globalmanager.get(cacheIdentifier);

        if (Ext.isObject(tab) && Ext.isFunction(tab.activate)) {
            tab.activate();
        }

        if (Ext.isFunction(callback)) {
            callback();
        }
    }
};


coreshop.order.helper.openSaleByNumberDialog = function(type, keyCode, e) {
    if (e['stopEvent']) {
        e.stopEvent();
    }

    Ext.MessageBox.prompt(t('coreshop_'+type+'_by_number'), t('coreshop_please_enter_the_number_of_the_' + type),
        function (button, value) {
            if (button === 'ok' && !Ext.isEmpty(value)) {
                coreshop.order.helper.openSaleByNumber(type, value);
            }
        }
    );
};


coreshop.order.helper.openSaleByNumber = function (type, number) {
    Ext.Ajax.request({
        url: '/admin/coreshop/'+type+'/find',
        params: {
            number: number
        },
        success: function (response) {
            var res = Ext.decode(response.responseText);
            if (res.success) {
                coreshop.order.helper.openSale(res.id, type);
            } else {
                Ext.MessageBox.alert(t('error'), t('element_not_found'));
            }
        }
    });
};

coreshop.order.helper.openOrder = function (id, callback) {
    coreshop.order.helper.openSale(id, 'order', callback);
};

coreshop.order.helper.openQuote = function (id, callback) {
    coreshop.order.helper.openSale(id, 'quote', callback);
};