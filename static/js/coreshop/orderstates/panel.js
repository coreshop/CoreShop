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

pimcore.registerNS('pimcore.plugin.coreshop.orderstates.panel');

pimcore.plugin.coreshop.orderstates.panel = Class.create(pimcore.plugin.coreshop.abstract.panel, {

    layoutId: 'coreshop_order_states_panel',
    storeId : 'coreshop_orderstates',
    iconCls : 'coreshop_icon_order_states',
    type : 'orderstates',

    url : {
        add : '/plugin/CoreShop/admin_order-state/add',
        delete : '/plugin/CoreShop/admin_order-state/delete',
        get : '/plugin/CoreShop/admin_order-state/get',
        list : '/plugin/CoreShop/admin_order-state/list'
    }
});
