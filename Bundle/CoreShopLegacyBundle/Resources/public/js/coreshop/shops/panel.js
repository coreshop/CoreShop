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

pimcore.registerNS('pimcore.plugin.coreshop.shops.panel');

pimcore.plugin.coreshop.shops.panel = Class.create(pimcore.plugin.coreshop.abstract.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_shops_panel',
    storeId : 'coreshop_shops',
    iconCls : 'coreshop_icon_shop',
    type : 'shops',

    url : {
        add : '/admin/CoreShop/shop/add',
        delete : '/admin/CoreShop/shop/delete',
        get : '/admin/CoreShop/shop/get',
        list : '/admin/CoreShop/shop/list'
    }
});
