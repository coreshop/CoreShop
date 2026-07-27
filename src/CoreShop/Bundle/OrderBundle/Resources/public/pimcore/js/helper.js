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

pimcore.registerNS('coreshop.order.helper');
pimcore.registerNS('coreshop.order.helper.x');

coreshop.order.helper.openSale = function (id, type, callback) {
    var cacheIdentifier = 'coreshop_'+type+'_' + id;

    if (pimcore.globalmanager.exists(cacheIdentifier) === false) {
        pimcore.globalmanager.add(cacheIdentifier, true);

        Ext.Ajax.request({
            url: Routing.generate('coreshop_admin_order_get_order'),
            params: {
                id: id,
                saleType: type
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
        url: Routing.generate('coreshop_admin_'+type+'_find'),
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

coreshop.order.helper.openCart = function (id, callback) {
    coreshop.order.helper.openSale(id, 'cart', callback);
};
