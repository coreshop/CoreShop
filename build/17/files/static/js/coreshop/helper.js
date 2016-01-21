/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
//pimcore.helpers.openElement = function (id, type, subtype) {

pimcore.registerNS("coreshop.helpers.x");

coreshop.helpers.openOrderByNumberDialog = function (keyCode, e) {

    if(e["stopEvent"]) {
        e.stopEvent();
    }

    Ext.MessageBox.prompt(t('coreshop_order_by_number'), t('coreshop_please_enter_the_number_of_the_order'),
        function (button, value) {
            if(button == "ok" && !Ext.isEmpty(value)) {
                coreshop.helpers.openOrderByNumber(value);
            }
        });
};

coreshop.helpers.openOrderByNumber = function(orderNumber) {
    Ext.Ajax.request({
        url: "/plugin/CoreShop/admin_Helper/get-order",
        params: {
            orderNumber: orderNumber
        },
        success: function (response) {
            var res = Ext.decode(response.responseText);
            if(res.success) {
                pimcore.helpers.openElement(res.id, "object", "CoreShopOrder");
            } else {
                Ext.MessageBox.alert(t("error"), t("element_not_found"));
            }
        }
    });
};

coreshop.helpers.openProductByArticleNumber = function(articleNumber) {

};