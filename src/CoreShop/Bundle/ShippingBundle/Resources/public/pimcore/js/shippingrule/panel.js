/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.shippingrule.panel');
coreshop.shippingrule.panel = Class.create(coreshop.rules.panel, {
    /**
     * @var string
     */
    layoutId: 'coreshop_carrier_shipping_rule_panel',
    storeId: 'coreshop_carrier_shipping_rules',
    iconCls: 'coreshop_icon_carrier_shipping_rule',
    type: 'coreshop_carriers_shipping_rules',

    url: {
        add: '/admin/coreshop/shipping_rules/add',
        delete: '/admin/coreshop/shipping_rules/delete',
        get: '/admin/coreshop/shipping_rules/get',
        list: '/admin/coreshop/shipping_rules/list',
        config: '/admin/coreshop/shipping_rules/get-config'
    },

    getItemClass: function () {
        return coreshop.shippingrule.item;
    }
});
