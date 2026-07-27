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

coreshop.helpers.showAbout = function () {

    var html = '<div class="pimcore_about_window">';
    html += '<br><img src="/bundles/coreshopcore/pimcore/img/logo-full.svg" style="width: 400px;"><br>';
    html += '<br><b>Version: ' + coreshop.settings.bundle.version + '</b>';
    html += '<br><br>&copy; by CoreShop GmbH, Wels, Austria (<a href="https://www.coreshop.com/" target="_blank">coreshop.org</a>)';
    html += '<br><br><a href="https://github.com/coreshop/coreshop/blob/master/LICENSE.md" target="_blank">License</a> | ';
    html += '<a href="https://www.coreshop.com/contact.html" target="_blank">Contact</a>';
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
