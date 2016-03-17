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
        add : '/plugin/CoreShop/admin_Currency/add',
        delete : '/plugin/CoreShop/admin_Currency/delete',
        get : '/plugin/CoreShop/admin_Currency/get',
        list : '/plugin/CoreShop/admin_Currency/list'
    }
});
