/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.customergroups.panel');

pimcore.plugin.coreshop.customergroups.panel = Class.create(pimcore.plugin.coreshop.abstract.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_customer_groups_panel',
    storeId : 'coreshop_customergroups',
    iconCls : 'coreshop_icon_customer_groups',
    type : 'customergroups',

    url : {
        add : '/plugin/CoreShop/admin_customer-group/add',
        delete : '/plugin/CoreShop/admin_customer-group/delete',
        get : '/plugin/CoreShop/admin_customer-group/get',
        list : '/plugin/CoreShop/admin_customer-group/list'
    }
});
