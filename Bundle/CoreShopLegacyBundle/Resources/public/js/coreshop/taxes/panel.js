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

pimcore.registerNS('pimcore.plugin.coreshop.taxes.panel');

pimcore.plugin.coreshop.taxes.panel = Class.create(pimcore.plugin.coreshop.abstract.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_taxes_panel',
    storeId : 'coreshop_taxes',
    iconCls : 'coreshop_icon_taxes',
    type : 'taxes',

    url : {
        add : '/admin/CoreShop/tax/add',
        delete : '/admin/CoreShop/tax/delete',
        get : '/admin/CoreShop/tax/get',
        list : '/admin/CoreShop/tax/list'
    }
});
