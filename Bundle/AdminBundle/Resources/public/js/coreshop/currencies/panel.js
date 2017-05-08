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

pimcore.registerNS('pimcore.plugin.coreshop.currencies.panel');

pimcore.plugin.coreshop.currencies.panel = Class.create(pimcore.plugin.coreshop.abstract.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_currencies_panel',
    storeId : 'coreshop_currencies',
    iconCls : 'coreshop_icon_currency',
    type : 'currencies',

    url : {
        add : '/admin/CoreShop/currencies/add',
        delete : '/admin/CoreShop/currencies/delete',
        get : '/admin/CoreShop/currencies/get',
        list : '/admin/CoreShop/currencies/list'
    }
});
