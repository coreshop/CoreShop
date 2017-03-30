/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.carriers.panel');

pimcore.plugin.coreshop.carriers.panel = Class.create(pimcore.plugin.coreshop.abstract.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_carriers_panel',
    storeId : 'coreshop_carriers',
    iconCls : 'coreshop_icon_carriers',
    type : 'carriers',

    url : {
        add : '/admin/CoreShop/carrier/add',
        delete : '/admin/CoreShop/carrier/delete',
        get : '/admin/CoreShop/carrier/get',
        list : '/admin/CoreShop/carrier/list'
    }
});
