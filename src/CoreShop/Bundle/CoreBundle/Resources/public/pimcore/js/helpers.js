/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */
//pimcore.helpers.openElement = function (id, type, subtype) {

pimcore.registerNS('coreshop.helpers.x');
pimcore.registerNS('coreshop.util.format.currency');

coreshop.helpers.long2ip = function (ip) {
    if (!isFinite(ip)) {
        return false
    }

    return [ip >>> 24, ip >>> 16 & 0xFF, ip >>> 8 & 0xFF, ip & 0xFF].join('.')
};

coreshop.helpers.createOrder = function () {
    pimcore.helpers.itemselector(
        false,
        function (customer) {
            new pimcore.plugin.coreshop.orders.create.order(customer.id);
        }.bind(this),
        {
            type: ['object'],
            subtype: {
                object: ['object']
            },
            specific: {
                classes: [coreshop.class_map.coreshop.customer]
            }
        }
    );
};

coreshop.helpers.openProductByArticleNumber = function (articleNumber) {

};

coreshop.util.format.currency = function (currency, v) {
    v = (Math.round(((v / 100) - 0) * 100)) / 100;
    v = (v == Math.floor(v)) ? v + '.00' : ((v * 10 == Math.floor(v * 10)) ? v + '0' : v);
    v = String(v);
    var ps = v.split('.'),
        whole = ps[0],
        sub = ps[1] ? '.' + ps[1] : '.00',
        r = /(\d+)(\d{3})/;
    while (r.test(whole)) {
        whole = whole.replace(r, '$1' + ',' + '$2');
    }

    v = whole + sub;
    if (v.charAt(0) == '-') {
        return '-' + currency + v.substr(1);
    }

    return currency + ' ' + v;
};

coreshop.helpers.showAbout = function () {

    var html = '<div class="pimcore_about_window">';
    html += '<br><img src="/bundles/coreshopcore/pimcore/img/logo.svg" style="width: 60px;"><br>';
    html += '<br><b>Version: ' + coreshop.settings.bundle.version + '</b>';
    html += '<br><br>&copy; by Dominik Pfaffenbauer, Wels, Austria (<a href="https://www.coreshop.org/" target="_blank">coreshop.org</a>)';
    html += '<br><br><a href="https://github.com/coreshop/coreshop/blob/master/LICENSE.md" target="_blank">License</a> | ';
    html += '<a href="https://www.coreshop.org/contact.html" target="_blank">Contact</a>';
    html += '</div>';

    var win = new Ext.Window({
        title: t('about'),
        width: 500,
        height: 300,
        bodyStyle: 'padding: 10px;',
        modal: true,
        html: html
    });

    win.show();
};

coreshop.helpers.constrastColor = function (color) {
    return (parseInt(color.replace('#', ''), 16) > 0xffffff / 2) ? 'black' : 'white';
};

coreshop.helpers.hexToRgb = function (hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? [
        parseInt(result[1], 16),
        parseInt(result[2], 16),
        parseInt(result[3], 16)
    ] : null;
};

coreshop.helpers.openMessagingThread = function (id) {
    var panelKey = 'coreshop_messaging_thread_' + id;

    if (pimcore.globalmanager.exists(panelKey) == false) {

        pimcore.globalmanager.add(panelKey, true);

        Ext.Ajax.request({
            url: '/admin/coreshop/messaging-thread/get',
            params: {
                id: id
            },
            success: function (response) {
                var res = Ext.decode(response.responseText);

                if (res.success) {
                    pimcore.globalmanager.add(panelKey, new pimcore.plugin.coreshop.messaging.thread.item(null, res.data, panelKey, panelKey, 'thread'));
                } else {
                    Ext.Msg.alert(t('open_target'), t('problem_opening_new_target'));
                }
            }.bind(this)
        });
    } else {
        var tab = pimcore.globalmanager.get('coreshop_messaging_thread_' + id);

        if (Ext.isObject(tab) && Ext.isFunction(tab.activate)) {
            tab.activate();
        }
    }
};

coreshop.helpers.requestNicePathData = function (targets, responseHandler) {
    var elementData = Ext.encode(targets);

    Ext.Ajax.request({
        method: 'POST',
        url: "/admin/coreshop/helper/get-nice-path",
        params: {
            targets: elementData
        },
        success: function (response) {
            try {
                var rdata = Ext.decode(response.responseText);
                if (rdata.success) {

                    var responseData = rdata.data;
                    responseHandler(responseData);
                }
            } catch (e) {
                console.log(e);
            }
        }.bind(this)
    });
};
