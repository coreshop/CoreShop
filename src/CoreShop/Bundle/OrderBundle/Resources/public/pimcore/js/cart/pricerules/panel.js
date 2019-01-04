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

pimcore.registerNS('coreshop.cart.pricerules.panel');

coreshop.cart.pricerules.panel = Class.create(coreshop.rules.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_price_rules_panel',
    storeId: 'coreshop_cart_price_rules',
    iconCls: 'coreshop_icon_price_rule',
    type: 'coreshop_cart_pricerules',

    url: {
        add: '/admin/coreshop/cart_price_rules/add',
        delete: '/admin/coreshop/cart_price_rules/delete',
        get: '/admin/coreshop/cart_price_rules/get',
        list: '/admin/coreshop/cart_price_rules/list',
        config: '/admin/coreshop/cart_price_rules/get-config'
    },

    getItemClass: function () {
        return coreshop.cart.pricerules.item;
    }
});
