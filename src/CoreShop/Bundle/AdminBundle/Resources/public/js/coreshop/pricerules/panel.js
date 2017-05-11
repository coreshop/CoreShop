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

pimcore.registerNS('pimcore.plugin.coreshop.pricerules.panel');

pimcore.plugin.coreshop.pricerules.panel = Class.create(pimcore.plugin.coreshop.rules.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_price_rules_panel',
    storeId : 'coreshop_cart_price_rules',
    iconCls : 'coreshop_icon_price_rule',
    type : 'cart_pricerules',

    url : {
        add : '/admin/CoreShop/cart_price_rules/add',
        delete : '/admin/CoreShop/cart_price_rules/delete',
        get : '/admin/CoreShop/cart_price_rules/get',
        list : '/admin/CoreShop/cart_price_rules/list',
        config : '/admin/CoreShop/cart_price_rules/get-config'
    },

    getItemClass : function () {
        return pimcore.plugin.coreshop.pricerules.item;
    }
});
