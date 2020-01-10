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
    html += '<br><img src="/bundles/coreshopcore/pimcore/img/logo-full.svg" style="width: 400px;"><br>';
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
