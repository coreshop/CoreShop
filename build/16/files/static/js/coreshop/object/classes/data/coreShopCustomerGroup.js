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

pimcore.registerNS("pimcore.object.classes.data.coreShopCustomerGroup");
pimcore.object.classes.data.coreShopCustomerGroup = Class.create(coreshop.object.classes.data.data, {

    type: "coreShopCustomerGroup",

    getTypeName: function () {
        return t("coreshop_customer_group");
    },

    getGroup: function () {
        return "coreshop";
    },

    getIconClass: function () {
        return "coreshop_icon_customer_groups";
    }
});
