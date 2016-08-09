/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.carrier.shippingrules.panel');
pimcore.plugin.coreshop.carrier.shippingrules.panel = Class.create(pimcore.plugin.coreshop.rules.panel, {
    /**
     * @var string
     */
    layoutId: 'coreshop_carrier_shipping_rule_panel',
    storeId : 'coreshop_carrier_shipping_rules',
    iconCls : 'coreshop_icon_carrier_shipping_rule',
    type : 'carriers_shipping_rules',
    
    url : {
        add : '/plugin/CoreShop/admin_carrier-shipping-rule/add',
        delete : '/plugin/CoreShop/admin_carrier-shipping-rule/delete',
        get : '/plugin/CoreShop/admin_carrier-shipping-rule/get',
        list : '/plugin/CoreShop/admin_carrier-shipping-rule/list',
        config : '/plugin/CoreShop/admin_carrier-shipping-rule/get-config'
    },

    getItemClass : function () {
        return pimcore.plugin.coreshop.carrier.shippingrules.item;
    }
});
